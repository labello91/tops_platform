<?php echo form_open(get_uri("custom_fields/save"), array("id" => "custom-field-form", "class" => "general-form", "role" => "form")); ?>
<div class="modal-body clearfix">
    <div class="container-fluid">
        <input type="hidden" name="related_to" value="<?php echo $related_to; ?>" />
        <?php echo view("custom_fields/form/input_fields"); ?>

        <?php if ($related_to != "events") { ?>
            <div class="form-group">
                <div class="row">
                    <label for="show_in_table" class=" col-md-3"><?php echo app_lang('show_in_table'); ?></label>
                    <div class="col-md-9">
                        <?php
                        echo form_checkbox(
                                "show_in_table", "1", $model_info->show_in_table, "id='show_in_table' class='form-check-input'"
                        );
                        ?>
                    </div>
                </div>
            </div>
        <?php } ?>

        <?php if ($related_to === "bu" || $related_to === "forecast") { ?>
            <div class="form-group">
                <div class="row">
                    <label for="show_in_forecast" class=" col-md-3"><?php echo app_lang('show_in_forecast'); ?></label>
                    <div class="col-md-9">
                        <?php
                        echo form_checkbox(
                                "show_in_forecast", "1", $model_info->show_in_forecast, "id='show_in_forecast' class='form-check-input'"
                        );
                        ?>
                    </div>
                </div>
            </div>
        <?php } ?>

        <?php if ($related_to != "events") { ?>
            <div class="form-group" id="visible_to_admins_only_container">
                <div class="row">
                    <label for="visible_to_admins_only" class=" col-md-3"><?php echo app_lang('visible_to_admins_only'); ?></label>
                    <div class="col-md-9">
                        <?php
                        echo form_checkbox(
                                "visible_to_admins_only", "1", $model_info->visible_to_admins_only, "id='visible_to_admins_only' class='form-check-input'"
                        );
                        ?>
                    </div>
                </div>
            </div>
        <?php } ?>

        <?php if ($related_to === "bu" || $related_to === "bu_contacts" || $related_to === "projects" || $related_to === "tasks" || $related_to === "tickets" || $related_to === "forecast") { ?>
            <div class="form-group" id="hide_from_bu_container">
                <div class="row">
                    <label for="hide_from_bu" class=" col-md-3"><?php echo app_lang('hide_from_bu'); ?></label>
                    <div class="col-md-9">
                        <?php
                        echo form_checkbox(
                                "hide_from_bu", "1", $model_info->hide_from_bu, "id='hide_from_bu' class='form-check-input'"
                        );
                        ?>
                    </div>
                </div>
            </div>
        <?php } ?>
        <?php if ($related_to === "bu" || $related_to === "bu_contacts") { ?>
            <div class="form-group" id="disable_editing_by_bu_container">
                <div class="row">
                    <label for="disable_editing_by_bu" class=" col-md-3"><?php echo app_lang('disable_editing_by_bu'); ?></label>
                    <div class="col-md-9">
                        <?php
                        echo form_checkbox(
                                "disable_editing_by_bu", "1", $model_info->disable_editing_by_bu, "id='disable_editing_by_bu' class='form-check-input'"
                        );
                        ?>
                    </div>
                </div>
            </div>
        <?php } ?>
        <?php if ($related_to === "intake") { ?>
            <div class="form-group">
                <div class="row">
                    <label for="show_on_kanban_card" class=" col-md-3"><?php echo app_lang('show_on_kanban_card'); ?></label>
                    <div class="col-md-9">
                        <?php
                        echo form_checkbox(
                                "show_on_kanban_card", "1", $model_info->show_on_kanban_card, "id='show_on_kanban_card' class='form-check-input'"
                        );
                        ?>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-default" data-bs-dismiss="modal"><span data-feather="x" class="icon-16"></span> <?php echo app_lang('close'); ?></button>
    <button type="submit" class="btn btn-primary"><span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang('save'); ?></button>
</div>

<?php echo form_close(); ?>


<script type="text/javascript">
    $(document).ready(function () {

        $("#custom-field-form").appForm({
            onSuccess: function (result) {
                window.location = "<?php echo get_uri("custom_fields/view/" . $related_to); ?>";
            }
        });

        showHideFields();
        $("#show_in_forecast, #visible_to_admins_only, #show_in_estimate, #hide_from_bu, #show_in_order").click(function () {
            showHideFields();
        });


        function showHideFields() {

            $("#hide_from_bu_container").show();
            $("#visible_to_admins_only_container").show();
            $("#disable_editing_by_bu_container").show();

            //if any field is visible to forecast, then it'll be availab for non-admins and bu
            if ($("#show_in_forecast").is(":checked")) {
                $("#hide_from_bu_container").hide();
                $("#visible_to_admins_only_container").hide();
            }

            if ($("#visible_to_admins_only").is(":checked")) {
                $("#hide_from_bu_container").hide();
                $("#disable_editing_by_bu_container").hide();
            }

            if ($("#hide_from_bu").is(":checked")) {
                $("#disable_editing_by_bu_container").hide();

            }
        }


        $("#example_variable_name").keydown(function (e) {
            //don't let the user to input space
            if (e.keyCode === 32) {
                e.preventDefault();
            }
        });
    });
</script>