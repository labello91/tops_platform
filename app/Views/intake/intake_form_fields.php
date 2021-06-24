<input type="hidden" name="id" value="<?php echo $model_info->id; ?>" />
<input type="hidden" name="view" value="<?php echo isset($view) ? $view : ""; ?>" />
<div class="form-group">
    <div class="row">
        <label for="bu_name" class="<?php echo $label_column; ?>"><?php echo app_lang('bu_name'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_input(array(
                "id" => "bu_name",
                "name" => "bu_name",
                "value" => $model_info->bu_name,
                "class" => "form-control",
                "placeholder" => app_lang('bu_name'),
                "autofocus" => true,
                "data-rule-required" => true,
                "data-msg-required" => app_lang("field_required"),
            ));
            ?>
        </div>
    </div>
</div>
<div class="form-group">
    <div class="row">
        <label for="intake_status_id" class="<?php echo $label_column; ?>"><?php echo app_lang('status'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            foreach ($statuses as $status) {
                $intake_status[$status->id] = $status->title;
            }

            echo form_dropdown("intake_status_id", $intake_status, array($model_info->intake_status_id), "class='select2'");
            ?>
        </div>
    </div>
</div>
<div class="form-group">
    <div class="row">
        <label for="owner_id" class="<?php echo $label_column; ?>"><?php echo app_lang('owner'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_input(array(
                "id" => "owner_id",
                "name" => "owner_id",
                "value" => $model_info->owner_id ? $model_info->owner_id : $login_user->id,
                "class" => "form-control",
                "placeholder" => app_lang('owner')
            ));
            ?>
        </div>
    </div>
</div>
<div class="form-group">
    <div class="row">
        <label for="intake_source_id" class="<?php echo $label_column; ?>"><?php echo app_lang('source'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            $intake_source = array();

            foreach ($sources as $source) {
                $intake_source[$source->id] = $source->title;
            }

            echo form_dropdown("intake_source_id", $intake_source, array($model_info->intake_source_id), "class='select2'");
            ?>
        </div>
    </div>
</div>
<div class="form-group">
    <div class="row">
        <label for="project_type" class="<?php echo $label_column; ?>"><?php echo app_lang('project_type'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_textarea(array(
                "id" => "project_type",
                "name" => "project_type",
                "value" => $model_info->project_type ? $model_info->project_type : "",
                "class" => "form-control",
                "placeholder" => app_lang('project_type')
            ));
            ?>

        </div>
    </div>
</div>
<div class="form-group">
    <div class="row">
        <label for="description" class="<?php echo $label_column; ?>"><?php echo app_lang('description'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_input(array(
                "id" => "description",
                "name" => "description",
                "value" => $model_info->description,
                "class" => "form-control",
                "placeholder" => app_lang('description')
            ));
            ?>
        </div>
    </div>
</div>
<div class="form-group">
    <div class="row">
        <label for="problematic" class="<?php echo $label_column; ?>"><?php echo app_lang('problematic'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_input(array(
                "id" => "problematic",
                "name" => "problematic",
                "value" => $model_info->problematic,
                "class" => "form-control",
                "placeholder" => app_lang('problematic')
            ));
            ?>
        </div>
    </div>
</div>
<div class="form-group">
    <div class="row">
        <label for="targeted_audience" class="<?php echo $label_column; ?>"><?php echo app_lang('targeted_audience'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_input(array(
                "id" => "targeted_audience",
                "name" => "targeted_audience",
                "value" => $model_info->targeted_audience,
                "class" => "form-control",
                "placeholder" => app_lang('targeted_audience')
            ));
            ?>
        </div>
    </div>
</div>
<div class="form-group">
    <div class="row">
        <label for="success_look" class="<?php echo $label_column; ?>"><?php echo app_lang('success_look'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_input(array(
                "id" => "success_look",
                "name" => "success_look",
                "value" => $model_info->success_look,
                "class" => "form-control",
                "placeholder" => app_lang('success_look')
            ));
            ?>
        </div>
    </div>
</div>
<div class="form-group">
    <div class="row">
        <label for="end_state" class="<?php echo $label_column; ?>"><?php echo app_lang('end_state'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_input(array(
                "id" => "end_state",
                "name" => "end_state",
                "value" => $model_info->end_state,
                "class" => "form-control",
                "placeholder" => app_lang('end_state')
            ));
            ?>
        </div>
    </div>
</div>
<div class="form-group">
    <div class="row">
        <label for="importance_rate" class="<?php echo $label_column; ?>"><?php echo app_lang('importance_rate'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_input(array(
                "id" => "importance_rate",
                "name" => "importance_rate",
                "value" => $model_info->importance_rate,
                "class" => "form-control",
                "placeholder" => app_lang('importance_rate')
            ));
            ?>
        </div>
    </div>
</div>
<div class="form-group">
    <div class="row">
        <label for="urgency_rate" class="<?php echo $label_column; ?>"><?php echo app_lang('urgency_rate'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_input(array(
                "id" => "urgency_rate",
                "name" => "urgency_rate",
                "value" => $model_info->urgency_rate,
                "class" => "form-control",
                "placeholder" => app_lang('urgency_rate')
            ));
            ?>
        </div>
    </div>
</div>
<div class="form-group">
    <div class="row">
        <label for="timeline" class="<?php echo $label_column; ?>"><?php echo app_lang('timeline'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_input(array(
                "id" => "timeline",
                "name" => "timeline",
                "value" => $model_info->timeline,
                "class" => "form-control",
                "placeholder" => app_lang('timeline')
            ));
            ?>
        </div>
    </div>
</div>


<?php if ($login_user->is_admin && get_setting("module_forecast")) { ?>
    <div class="form-group">
        <div class="row">
            <label for="currency" class="<?php echo $label_column; ?>"><?php echo app_lang('currency'); ?></label>
            <div class="<?php echo $field_column; ?>">
                <?php
                echo form_input(array(
                    "id" => "currency",
                    "name" => "currency",
                    "value" => $model_info->currency,
                    "class" => "form-control",
                    "placeholder" => app_lang('keep_it_blank_to_use_default') . " (" . get_setting("default_currency") . ")"
                ));
                ?>
            </div>
        </div> 
    </div> 
    <div class="form-group">
        <div class="row">
            <label for="currency_symbol" class="<?php echo $label_column; ?>"><?php echo app_lang('currency_symbol'); ?></label>
            <div class="<?php echo $field_column; ?>">
                <?php
                echo form_input(array(
                    "id" => "currency_symbol",
                    "name" => "currency_symbol",
                    "value" => $model_info->currency_symbol,
                    "class" => "form-control",
                    "placeholder" => app_lang('keep_it_blank_to_use_default') . " (" . get_setting("currency_symbol") . ")"
                ));
                ?>
            </div>
        </div>
    </div>
<?php } ?>

<?php echo view("custom_fields/form/prepare_context_fields", array("custom_fields" => $custom_fields, "label_column" => $label_column, "field_column" => $field_column)); ?> 

<script type="text/javascript">
    $(document).ready(function () {
        $('[data-bs-toggle="tooltip"]').tooltip();
        $(".select2").select2();

<?php if (isset($currency_dropdown)) { ?>
            if ($('#currency').length) {
                $('#currency').select2({data: <?php echo json_encode($currency_dropdown); ?>});
            }
<?php } ?>

        $('#owner_id').select2({data: <?php echo json_encode($owners_dropdown); ?>});

    });
</script>