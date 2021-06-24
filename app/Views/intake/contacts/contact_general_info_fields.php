<input type="hidden" name="contact_id" value="<?php echo $model_info->id; ?>" />
<input type="hidden" name="bu_id" value="<?php echo $model_info->bu_id; ?>" />
<div class="form-group">
    <div class="row">
        <?php
        $label_column = isset($label_column) ? $label_column : "col-md-3";
        $field_column = isset($field_column) ? $field_column : "col-md-9";
        ?>
        <label for="first_name" class="<?php echo $label_column; ?>"><?php echo app_lang('first_name'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_input(array(
                "id" => "first_name",
                "name" => "first_name",
                "value" => $model_info->first_name,
                "class" => "form-control",
                "placeholder" => app_lang('first_name'),
                "data-rule-required" => true,
                "data-msg-required" => app_lang("field_required"),
            ));
            ?>
        </div>
    </div>
</div>
<div class="form-group">
    <div class="row">
        <label for="last_name" class="<?php echo $label_column; ?>"><?php echo app_lang('last_name'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_input(array(
                "id" => "last_name",
                "name" => "last_name",
                "value" => $model_info->last_name,
                "class" => "form-control",
                "placeholder" => app_lang('last_name'),
                "data-rule-required" => true,
                "data-msg-required" => app_lang("field_required"),
            ));
            ?>
        </div>
    </div>
</div>
<div class="form-group">
    <div class="row">
        <label for="email" class="<?php echo $label_column; ?>"><?php echo app_lang('email'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_input(array(
                "id" => "email",
                "name" => "email",
                "value" => $model_info->email,
                "class" => "form-control",
                "placeholder" => app_lang('email'),
                "data-rule-email" => true,
                "data-msg-email" => app_lang("enter_valid_email"),
                "data-rule-required" => true,
                "data-msg-required" => app_lang("field_required"),
                "autocomplete" => "off"
            ));
            ?>
        </div>
    </div>
</div>
<div class="form-group">
    <div class="row">
        <label for="username" class="<?php echo $label_column; ?>"><?php echo app_lang('slack_username'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_input(array(
                "id" => "username",
                "name" => "username",
                "value" => $model_info->username ? $model_info->username : "",
                "class" => "form-control",
                "placeholder" => app_lang('slack_username')
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
        <label for="gender" class="<?php echo $label_column; ?>"><?php echo app_lang('gender'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_radio(array(
                "id" => "gender_male",
                "name" => "gender",
                "class" => "form-check-input",
                    ), "male", ($model_info->gender == "male") ? true : false);
            ?>
            <label for="gender_male" class="mr15 p0"><?php echo app_lang('male'); ?></label> <?php
            echo form_radio(array(
                "id" => "gender_female",
                "name" => "gender",
                "class" => "form-check-input",
                    ), "female", ($model_info->gender == "female") ? true : false);
            ?>
            <label for="gender_female" class="p0"><?php echo app_lang('female'); ?></label>
        </div>
    </div>
</div>

<?php echo view("custom_fields/form/prepare_context_fields", array("custom_fields" => $custom_fields, "label_column" => $label_column, "field_column" => $field_column)); ?> 

<?php if ($login_user->is_admin && $model_info->id) { ?>
    <div class="form-group ">
        <div class="row">
            <label for="is_primary_contact"  class="<?php echo $label_column; ?>"><?php echo app_lang('primary_contact'); ?></label>

            <div class="<?php echo $field_column; ?>">
                <?php
                //is set primary contact, disable the checkbox
                $disable = "";
                if ($model_info->is_primary_contact) {
                    $disable = "disabled='disabled'";
                }
                echo form_checkbox("is_primary_contact", "1", $model_info->is_primary_contact, "id='is_primary_contact' class='form-check-input' $disable");
                ?> 
            </div>
        </div>
    </div>
<?php } ?> 