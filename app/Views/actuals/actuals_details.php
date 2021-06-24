<div class="modal-body clearfix general-form">

    <div class="container-fluid">
        <div class="clearfix">
            <div class="row">
                <div class="col-md-12 mb20">
                    <strong class="font-18"><?php echo app_lang("actuals") . " # " . format_to_date($actuals_info->actuals_date, false); ?></strong>
                    <div>
                        <?php
                        if ($actuals_info->amount) {
                            //prepare amount 

                            $total_amount = to_currency($actuals_info->amount);

                            echo "<span class='font-14'>$total_amount</span> ";
                        }
                        ?>
                    </div>
                </div>

                <div class="col-md-12 mb15">
                    <strong><?php echo $actuals_info->title; ?></strong>
                </div>

                <div class="col-md-12 mb15">
                    <?php echo $actuals_info->description ? nl2br(link_it($actuals_info->description)) : "-"; ?>
                </div>

                <?php if ($actuals_info->category_title) { ?>
                    <div class="col-md-12 mb15">
                        <strong><?php echo app_lang('category') . ": "; ?></strong> <?php echo $actuals_info->category_title; ?>
                    </div>
                <?php } ?>

                <?php if ($actuals_info->project_title) { ?>
                    <div class="col-md-12 mb15">
                        <strong><?php echo app_lang('project') . ": "; ?> </strong> <?php echo anchor(get_uri("projects/view/" . $actuals_info->project_id), $actuals_info->project_title); ?>
                    </div>
                <?php } ?>

                <?php if ($actuals_info->linked_user_name) { ?>
                    <div class="col-md-12 mb15">
                        <strong><?php echo app_lang('team_member') . ": "; ?> </strong> <?php echo get_team_member_profile_link($actuals_info->user_id, $actuals_info->linked_user_name); ?>
                    </div>
                <?php } ?>

                <?php
                if (count($custom_fields_list)) {
                    foreach ($custom_fields_list as $data) {
                        if ($data->value) {
                            ?>
                            <div class="col-md-12 mb15">
                                <strong><?php echo $data->title . ": "; ?> </strong> <?php echo view("custom_fields/output_" . $data->field_type, array("value" => $data->value)); ?>
                            </div>
                            <?php
                        }
                    }
                }
                ?>

                <?php if ($actuals_info->recurring_actuals_id) { ?>
                    <div class="col-md-12 mb15">
                        <strong><?php echo app_lang('created_from') . ": "; ?> </strong> 
                        <?php
                        echo modal_anchor(get_uri("actuals/actuals_details"), app_lang("original_actuals"), array("title" => app_lang("actuals_details"), "data-post-id" => $actuals_info->recurring_actuals_id));
                        ?>
                    </div>
                <?php } ?>

                <!--recurring info-->
                <?php if ($actuals_info->recurring) { ?>

                    <?php
                    $recurring_stopped = false;
                    $recurring_cycle_class = "";
                    if ($actuals_info->no_of_cycles_completed > 0 && $actuals_info->no_of_cycles_completed == $actuals_info->no_of_cycles) {
                        $recurring_stopped = true;
                        $recurring_cycle_class = "text-danger";
                    }
                    ?>

                    <?php
                    $cycles = $actuals_info->no_of_cycles_completed . "/" . $actuals_info->no_of_cycles;
                    if (!$actuals_info->no_of_cycles) { //if not no of cycles, so it's infinity
                        $cycles = $actuals_info->no_of_cycles_completed . "/&#8734;";
                    }
                    ?>

                    <div class="col-md-12 mb15">
                        <strong><?php echo app_lang("repeat_every") . ": "; ?> </strong> <?php echo $actuals_info->repeat_every . " " . app_lang("interval_" . $actuals_info->repeat_type); ?>
                    </div>

                    <div class="col-md-12 mb15">
                        <strong><?php echo app_lang("cycles") . ": "; ?> </strong> <span class="<?php echo $recurring_cycle_class; ?>"><?php echo $cycles; ?></span>
                    </div>

                    <?php if (!$recurring_stopped && (int) $actuals_info->next_recurring_date) { ?>
                        <div class="col-md-12 mb15">
                            <strong><?php echo app_lang("next_recurring_date") . ": "; ?> </strong> <?php echo format_to_date($actuals_info->next_recurring_date, false); ?>
                        </div>
                    <?php } ?>

                <?php } ?>

            </div>
        </div>

    </div>
</div>

<div class="modal-footer">
    <?php echo modal_anchor(get_uri("actuals/modal_form"), "<i data-feather='edit' class='icon-16'></i> " . app_lang('edit_actuals'), array("class" => "btn btn-default", "data-post-id" => $actuals_info->id, "title" => app_lang('edit_actuals'))); ?>
    <button type="button" class="btn btn-default" data-bs-dismiss="modal"><span data-feather="x" class="icon-16"></span> <?php echo app_lang('close'); ?></button>
</div>
