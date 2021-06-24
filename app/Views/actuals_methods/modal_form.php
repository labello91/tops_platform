<?php echo form_open(get_uri("actuals_methods/save"), array("id" => "actuals-method-form", "class" => "general-form", "role" => "form")); ?>
<div class="modal-body clearfix">
    <div class="container-fluid">
        <input type="hidden" name="id" value="<?php echo $model_info->id; ?>" />
        <div class="form-group">
            <div class="row">
                <label for="title" class=" col-md-4"><?php echo app_lang('title'); ?></label>
                <div class=" col-md-8">
                    <?php
                    echo form_input(array(
                        "id" => "title",
                        "name" => "title",
                        "value" => $model_info->title,
                        "class" => "form-control",
                        "placeholder" => app_lang('title'),
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
                <label for="description" class="col-md-4"><?php echo app_lang('description'); ?></label>
                <div class=" col-md-8">
                    <?php
                    echo form_textarea(array(
                        "id" => "description",
                        "name" => "description",
                        "value" => $model_info->description,
                        "class" => "form-control",
                        "placeholder" => app_lang('description'),
                        "data-rich-text-editor" => true
                    ));
                    ?>
                </div>
            </div>
        </div>
        <?php if ($model_info->online_payable == 1) { ?>
            <div class="form-group">
                <div class="row">
                    <label for="available_on_forecast" class="col-md-4"><?php echo app_lang('available_on_forecast'); ?>
                        <span class="help" data-bs-toggle="tooltip" title="<?php echo app_lang('available_on_forecast_help_text'); ?>"><i data-feather="help-circle" class="icon-16"></i></span>
                    </label>
                    <div class="col-md-8">
                        <?php
                        echo form_checkbox("available_on_forecast", "1", $model_info->available_on_forecast, "id='available_on_forecast' class='form-check-input'");
                        ?> 
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <label for="minimum_actuals_amount" class="col-md-4"><?php echo app_lang('minimum_actuals_amount'); ?>
                        <span class="help" data-bs-toggle="tooltip" title="<?php echo app_lang('minimum_actuals_amount_help_text'); ?>"><i data-feather="help-circle" class="icon-16"></i></span>
                    </label>
                    <div class="col-md-8">
                        <?php
                        echo form_input(array(
                            "id" => "minimum_actuals_amount",
                            "name" => "minimum_actuals_amount",
                            "value" => $model_info->minimum_actuals_amount ? to_decimal_format($model_info->minimum_actuals_amount) : 0,
                            "class" => "form-control",
                            "placeholder" => app_lang('minimum_actuals_amount')
                        ));
                        ?>
                    </div>
                </div>
            </div>
            <?php
            if (count($settings)) {
                foreach ($settings as $setting) {
                    ?>

                    <div class="form-group">
                        <div class="row">
                            <label for="<?php echo get_array_value($setting, "name"); ?>" class="col-md-4"><?php
                                echo get_array_value($setting, "text");
                                if (get_array_value($setting, "help_text")) {
                                    ?>
                                    <span class="help" data-bs-toggle="tooltip" title="<?php echo get_array_value($setting, "help_text"); ?>"><i data-feather="help-circle" class="icon-16"></i></span>
                                <?php }
                                ?>

                            </label>
                            <div class="col-md-8">
                                <?php
                                $field_type = get_array_value($setting, "type");
                                $setting_name = get_array_value($setting, "name");

                                if ($field_type == "text") {
                                    echo form_input(array(
                                        "id" => $setting_name,
                                        "name" => $setting_name,
                                        "value" => $model_info->$setting_name,
                                        "class" => "form-control",
                                        "placeholder" => get_array_value($setting, "text"),
                                        "data-rule-required" => true,
                                        "data-msg-required" => app_lang("field_required")
                                    ));
                                } else if ($field_type == "boolean") {
                                    echo form_checkbox($setting_name, "1", $model_info->$setting_name == "1" ? true : false, "id='$setting_name' class='form-check-input'");
                                } else if ($field_type == "readonly") {
                                    echo $model_info->$setting_name;
                                }
                                ?> 
                            </div>
                        </div>
                    </div>
                    <?php
                }
            }
        }
        ?>

    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-default" data-bs-dismiss="modal"><span data-feather="x" class="icon-16"></span> <?php echo app_lang('close'); ?></button>
    <button type="submit" class="btn btn-primary"><span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang('save'); ?></button>
</div>
<?php echo form_close(); ?>

<script type="text/javascript">
    $(document).ready(function () {
        $("#actuals-method-form").appForm({
            onSuccess: function (result) {
                $("#actuals-method-table").appTable({newData: result.data, dataId: result.id});
            }
        });
        setTimeout(function () {
            $("#title").focus();
        }, 200);
        $('[data-bs-toggle="tooltip"]').tooltip();
    });
</script>    