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

<?php if ($login_user->is_admin || get_array_value($login_user->permissions, "bu") === "all") { ?>
    <div class="form-group">
        <div class="row">
            <label for="created_by" class="<?php echo $label_column; ?>"><?php echo app_lang('owner'); ?></label>
            <div class="<?php echo $field_column; ?>">
                <?php
                echo form_input(array(
                    "id" => "created_by",
                    "name" => "created_by",
                    "value" => $model_info->created_by ? $model_info->created_by : $login_user->id,
                    "class" => "form-control",
                    "placeholder" => app_lang('owner'),
                    "data-rule-required" => true,
                    "data-msg-required" => app_lang("field_required")
                ));
                ?>
            </div>
        </div>
    </div>
<?php } ?>

<div class="form-group">
    <div class="row">
        <label for="slack_username" class="<?php echo $label_column; ?>"><?php echo app_lang('slack_username'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_textarea(array(
                "id" => "slack_username",
                "name" => "username",
                "value" => $model_info->username ? $model_info->username : "",
                "class" => "form-control",
                "placeholder" => app_lang('username')
            ));
            ?>

        </div>
    </div>
</div>
<div class="form-group">
    <div class="row">
        <label for="job_title" class="<?php echo $label_column; ?>"><?php echo app_lang('job_title'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_input(array(
                "id" => "job_title",
                "name" => "job_title",
                "value" => $model_info->job_title,
                "class" => "form-control",
                "placeholder" => app_lang('job_title')
            ));
            ?>
        </div>
    </div>
</div>
<div class="form-group">
    <div class="row">
        <label for="country" class="<?php echo $label_column; ?>"><?php echo app_lang('country'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_input(array(
                "id" => "country",
                "name" => "country",
                "value" => $model_info->country,
                "class" => "form-control",
                "placeholder" => app_lang('country')
            ));
            ?>
        </div>
    </div>
</div>
<div class="form-group">
    <div class="row">
        <label for="phone" class="<?php echo $label_column; ?>"><?php echo app_lang('phone'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_input(array(
                "id" => "phone",
                "name" => "phone",
                "value" => $model_info->phone,
                "class" => "form-control",
                "placeholder" => app_lang('phone')
            ));
            ?>
        </div>
    </div>
</div>


<?php if ($login_user->user_type === "team_member") { ?>
    <div class="form-group">
        <div class="row">
            <label for="groups" class="<?php echo $label_column; ?>"><?php echo app_lang('bu_groups'); ?></label>
            <div class="<?php echo $field_column; ?>">
                <?php
                echo form_input(array(
                    "id" => "group_ids",
                    "name" => "group_ids",
                    "value" => $model_info->group_ids,
                    "class" => "form-control",
                    "placeholder" => app_lang('bu_groups')
                ));
                ?>
            </div>
        </div>
    </div>
<?php } ?>


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

<?php if (isset($currency_dropdown)) { ?>
            if ($('#currency').length) {
                $('#currency').select2({data: <?php echo json_encode($currency_dropdown); ?>});
            }
<?php } ?>

<?php if (isset($groups_dropdown)) { ?>
            $("#group_ids").select2({
                multiple: true,
                data: <?php echo json_encode($groups_dropdown); ?>
            });
<?php } ?>

<?php if ($login_user->is_admin || get_array_value($login_user->permissions, "bu") === "all") { ?>
            $('#created_by').select2({data: <?php echo $team_members_dropdown; ?>});
<?php } ?>

    });
</script>