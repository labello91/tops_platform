<?php echo form_open(get_uri("forecast/save"), array("id" => "forecast-form", "class" => "general-form", "role" => "form")); ?>
<div id="forecast-dropzone" class="post-dropzone">
    <div class="modal-body clearfix">
        <div class="container-fluid">
            <input type="hidden" name="id" value="<?php echo $model_info->id; ?>" />

            <?php if ($bu_id && !$project_id) { ?>
                <input type="hidden" name="forecast_bu_id" value="<?php echo $bu_id; ?>" />
            <?php } else { ?>
                <div class="form-group">
                    <div class="row">
                        <label for="forecast_bu_id" class=" col-md-3"><?php echo app_lang('bu'); ?></label>
                        <div class="col-md-9">
                            <?php
                            echo form_dropdown("forecast_bu_id", $bus_dropdown, array($model_info->bu_id), "class='select2 validate-hidden' id='forecast_bu_id' data-rule-required='true', data-msg-required='" . app_lang('field_required') . "'");
                            ?>
                        </div>
                    </div>
                </div>
            <?php } ?>
            <?php if ($project_id) { ?>
                <input type="hidden" name="forecast_project_id" value="<?php echo $project_id; ?>" />
            <?php } else { ?>
                <div class="form-group">
                    <div class="row">
                        <label for="forecast_project_id" class=" col-md-3"><?php echo app_lang('project'); ?></label>
                        <div class="col-md-9" id="forecast-porject-dropdown-section">
                            <?php
                            echo form_input(array(
                                "id" => "forecast_project_id",
                                "name" => "forecast_project_id",
                                "value" => $model_info->project_id,
                                "class" => "form-control",
                                "placeholder" => app_lang('project')
                            ));
                            ?>
                        </div>
                    </div>
                </div>
            <?php } ?>

            
            <div class="form-group">
                <div class="row">
                    <label for="forecast_recurring" class=" col-md-3"><?php echo app_lang('recurring'); ?>  <span class="help" data-bs-toggle="tooltip" title="<?php echo app_lang('cron_job_required'); ?>"><i data-feather="help-circle" class="icon-16"></i></span></label>
                    <div class=" col-md-9">
                        <?php
                        echo form_checkbox("recurring", "1", $model_info->recurring ? true : false, "id='forecast_recurring' class='form-check-input'");
                        ?>                       
                    </div>
                </div>
            </div>    
            <div id="recurring_fields" class="<?php if (!$model_info->recurring) echo "hide"; ?>"> 
                <div class="form-group">
                    <div class="row">
                        <label for="repeat_every" class=" col-md-3"><?php echo app_lang('repeat_every'); ?></label>
                        <div class="col-md-4">
                            <?php
                            echo form_input(array(
                                "id" => "repeat_every",
                                "name" => "repeat_every",
                                "type" => "number",
                                "value" => $model_info->repeat_every ? $model_info->repeat_every : 1,
                                "min" => 1,
                                "class" => "form-control recurring_element",
                                "placeholder" => app_lang('repeat_every'),
                                "data-rule-required" => true,
                                "data-msg-required" => app_lang("field_required")
                            ));
                            ?>
                        </div>
                        <div class="col-md-5">
                            <?php
                            echo form_dropdown(
                                    "repeat_type", array(
                                "days" => app_lang("interval_days"),
                                "weeks" => app_lang("interval_weeks"),
                                "months" => app_lang("interval_months"),
                                "years" => app_lang("interval_years"),
                                    ), $model_info->repeat_type ? $model_info->repeat_type : "months", "class='select2 recurring_element' id='repeat_type'"
                            );
                            ?>
                        </div>
                    </div>
                </div>    

                <div class="form-group">
                    <div class="row">
                        <label for="no_of_cycles" class=" col-md-3"><?php echo app_lang('cycles'); ?></label>
                        <div class="col-md-4">
                            <?php
                            echo form_input(array(
                                "id" => "no_of_cycles",
                                "name" => "no_of_cycles",
                                "type" => "number",
                                "min" => 1,
                                "value" => $model_info->no_of_cycles ? $model_info->no_of_cycles : "",
                                "class" => "form-control",
                                "placeholder" => app_lang('cycles')
                            ));
                            ?>
                        </div>
                        <div class="col-md-5 mt5">
                            <span class="help" data-bs-toggle="tooltip" title="<?php echo app_lang('recurring_cycle_instructions'); ?>"><i data-feather="help-circle" class="icon-16"></i></span>
                        </div>
                    </div>
                </div>  



                <div class = "form-group hide" id = "next_recurring_date_container" >
                    <div class="row">
                        <label for = "next_recurring_date" class = " col-md-3"><?php echo app_lang('next_recurring_date'); ?>  </label>
                        <div class=" col-md-9">
                            <?php
                            echo form_input(array(
                                "id" => "next_recurring_date",
                                "name" => "next_recurring_date",
                                "class" => "form-control",
                                "placeholder" => app_lang('next_recurring_date'),
                                "autocomplete" => "off",
                                "data-rule-required" => true,
                                "data-msg-required" => app_lang("field_required"),
                            ));
                            ?>
                        </div>
                    </div>
                </div>

            </div>  
            <div class="form-group">
                <div class="row">
                    <label for="forecast_note" class=" col-md-3"><?php echo app_lang('note'); ?></label>
                    <div class=" col-md-9">
                        <?php
                        echo form_textarea(array(
                            "id" => "forecast_note",
                            "name" => "forecast_note",
                            "value" => $model_info->note ? $model_info->note : "",
                            "class" => "form-control",
                            "placeholder" => app_lang('note'),
                            "data-rich-text-editor" => true
                        ));
                        ?>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <label for="forecast_labels" class=" col-md-3"><?php echo app_lang('labels'); ?></label>
                    <div class=" col-md-9">
                        <?php
                        echo form_input(array(
                            "id" => "forecast_labels",
                            "name" => "labels",
                            "value" => $model_info->labels,
                            "class" => "form-control",
                            "placeholder" => app_lang('labels')
                        ));
                        ?>
                    </div>
                </div>
            </div>

            <?php echo view("custom_fields/form/prepare_context_fields", array("custom_fields" => $custom_fields, "label_column" => "col-md-3", "field_column" => " col-md-9")); ?> 


            <?php if ($is_clone) { ?>
                <div class="form-group">
                    <div class="row">
                        <label for="copy_items"class=" col-md-12">
                            <?php
                            echo form_checkbox("copy_items", "1", true, "id='copy_items' disabled='disabled' class='form-check-input float-start mr15'");
                            ?>    
                            <?php echo app_lang('copy_items'); ?>
                        </label>
                    </div>
                </div>
            <?php } ?>

            <div class="form-group">
                <div class="row">
                    <div class="col-md-12">
                        <?php
                        echo view("includes/file_list", array("files" => $model_info->files));
                        ?>
                    </div>
                </div>
            </div>

            <?php echo view("includes/dropzone_preview"); ?>
        </div>
    </div>

    <div class="modal-footer">
        <button class="btn btn-default upload-file-button float-start btn-sm round me-auto" type="button" style="color:#7988a2"><i data-feather="camera" class="icon-16"></i> <?php echo app_lang("upload_file"); ?></button>
        <button type="button" class="btn btn-default" data-bs-dismiss="modal"><span data-feather="x" class="icon-16"></span> <?php echo app_lang('close'); ?></button>
        <button type="submit" class="btn btn-primary"><span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang('save'); ?></button>
    </div>
</div>
<?php echo form_close(); ?>

<script type="text/javascript">
    $(document).ready(function () {
        if ("<?php echo $forecast_id; ?>" || "<?php echo $actuals_id; ?>") {
            RELOAD_VIEW_AFTER_UPDATE = false; //go to forecast/order page
        }

        var uploadUrl = "<?php echo get_uri("forecast/upload_file"); ?>";
        var validationUri = "<?php echo get_uri("forecast/validate_forecast_file"); ?>";

        var dropzone = attachDropzoneWithForm("#forecast-dropzone", uploadUrl, validationUri);

        $("#forecast-form").appForm({
            onSuccess: function (result) {
                if (typeof RELOAD_VIEW_AFTER_UPDATE !== "undefined" && RELOAD_VIEW_AFTER_UPDATE) {
                    location.reload();
                } else {
                    window.location = "<?php echo site_url('forecast/view'); ?>/" + result.id;
                }
            },
            onAjaxSuccess: function (result) {
                if (!result.success && result.next_recurring_date_error) {
                    $("#next_recurring_date").val(result.next_recurring_date_value);
                    $("#next_recurring_date_container").removeClass("hide");

                    $("#forecast-form").data("validator").showErrors({
                        "next_recurring_date": result.next_recurring_date_error
                    });
                }
            }
        });
        $("#forecast-form .tax-select2").select2();
        $("#repeat_type").select2();

        $("#forecast_labels").select2({multiple: true, data: <?php echo json_encode($label_suggestions); ?>});

        setDatePicker("#forecast_date, #forecast_due_date");

        //load all projects of selected bu
        $("#forecast_bu_id").select2().on("change", function () {
            var bu_id = $(this).val();
            if ($(this).val()) {
                $('#forecast_project_id').select2("destroy");
                $("#forecast_project_id").hide();
                appLoader.show({container: "#forecast-porject-dropdown-section"});
                $.ajax({
                    url: "<?php echo get_uri("forecast/get_project_suggestion") ?>" + "/" + bu_id,
                    dataType: "json",
                    success: function (result) {
                        $("#forecast_project_id").show().val("");
                        $('#forecast_project_id').select2({data: result});
                        appLoader.hide();
                    }
                });
            }
        });

        $('#forecast_project_id').select2({data: <?php echo json_encode($projects_suggestion); ?>});

        if ("<?php echo $project_id; ?>") {
            $("#forecast_bu_id").select2("readonly", true);
        }

        //show/hide recurring fields
        $("#forecast_recurring").click(function () {
            if ($(this).is(":checked")) {
                $("#recurring_fields").removeClass("hide");
            } else {
                $("#recurring_fields").addClass("hide");
            }
        });

        setDatePicker("#next_recurring_date", {
            startDate: moment().add(1, 'days').format("YYYY-MM-DD") //set min date = tomorrow
        });


        $('[data-bs-toggle="tooltip"]').tooltip();

        var defaultDue = "<?php echo get_setting('default_due_date_after_billing_date'); ?>";
        var id = "<?php echo $model_info->id; ?>";

        //disable this operation in edit mode
        if (defaultDue && !id) {
            //for auto fill the due date based on forecast date
            setDefaultDueDate = function () {
                var dateFormat = getJsDateFormat().toUpperCase();

                var forecastDate = $('#forecast_date').val();
                var dueDate = moment(forecastDate, dateFormat).add(defaultDue, 'days').format(dateFormat);
                $("#forecast_due_date").val(dueDate);

            };

            $("#forecast_date").change(function () {
                setDefaultDueDate();
            });

            setDefaultDueDate();
        }

    });
</script>