<div id="page-content" class="page-wrapper clearfix">
    <div class="row">
        <div class="col-sm-3 col-lg-2">
            <?php
            $tab_view['active_tab'] = "forecast";
            echo view("settings/tabs", $tab_view);
            ?>
        </div>

        <div class="col-sm-9 col-lg-10">
            <?php echo form_open(get_uri("settings/save_forecast_settings"), array("id" => "forecast-settings-form", "class" => "general-form dashed-row", "role" => "form")); ?>
            <div class="card">
                <div class=" card-header">
                    <h4><?php echo app_lang("forecast_settings"); ?></h4>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <div class="row">
                            <label for="logo" class=" col-md-2"><?php echo app_lang('forecast_logo'); ?> (300x100) </label>
                            <div class=" col-md-10">
                                <div class="float-start mr15">
                                    <img id="forecast-logo-preview" src="<?php echo get_file_from_setting('forecast_logo'); ?>" alt="..." />
                                </div>
                                <div class="float-start file-upload btn btn-default btn-sm">
                                    <i data-feather='upload' class="icon-14"></i> <?php echo app_lang("upload_and_crop"); ?>
                                    <input id="forecast_logo_file" class="cropbox-upload upload" name="forecast_logo_file" type="file" data-height="100" data-width="300" data-preview-container="#forecast-logo-preview" data-input-field="#forecast_logo" />
                                </div>
                                <div class="mt10 ml10 float-start">
                                    <?php
                                    echo form_upload(array(
                                        "id" => "forecast_logo_file_upload",
                                        "name" => "forecast_logo_file",
                                        "class" => "no-outline hidden-input-file"
                                    ));
                                    ?>
                                    <label for="forecast_logo_file_upload" class="btn btn-default btn-sm">
                                        <i data-feather='upload' class="icon-14"></i> <?php echo app_lang("upload"); ?>
                                    </label>
                                </div>
                                <input type="hidden" id="forecast_logo" name="forecast_logo" value=""  />
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <label for="forecast_prefix" class=" col-md-2"><?php echo app_lang('forecast_prefix'); ?></label>
                            <div class=" col-md-10">
                                <?php
                                echo form_input(array(
                                    "id" => "forecast_prefix",
                                    "name" => "forecast_prefix",
                                    "value" => get_setting("forecast_prefix"),
                                    "class" => "form-control",
                                    "placeholder" => strtoupper(app_lang("forecast")) . " #"
                                ));
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <label for="forecast_color" class=" col-md-2"><?php echo app_lang('forecast_color'); ?></label>
                            <div class=" col-md-10">
                                <?php
                                echo form_input(array(
                                    "id" => "forecast_color",
                                    "name" => "forecast_color",
                                    "value" => get_setting("forecast_color"),
                                    "class" => "form-control",
                                    "placeholder" => "Ex. #e2e2e2"
                                ));
                                ?>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="row">
                            <label for="forecast_footer" class=" col-md-2"><?php echo app_lang('forecast_footer'); ?></label>
                            <div class=" col-md-10">
                                <?php
                                echo form_textarea(array(
                                    "id" => "forecast_footer",
                                    "name" => "forecast_footer",
                                    "value" => get_setting("forecast_footer"),
                                    "class" => "form-control"
                                ));
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <label for="forecast_style" class=" col-md-2"><?php echo app_lang('forecast_style'); ?></label>
                            <div class="col-md-10">
                                <?php
                                $forecast_style = get_setting("forecast_style") ? get_setting("forecast_style") : "style_1";
                                ?>
                                <input type="hidden" id="forecast_style" name="forecast_style" value="<?php echo $forecast_style; ?>" />

                                <div class="clearfix forecast-styles">
                                    <div data-value="style_1" class="item <?php echo $forecast_style != 'style_2' ? ' active ' : ''; ?>" >
                                        <span class="selected-mark <?php echo $forecast_style != 'style_2' ? '' : 'hide'; ?>"><i data-feather="check-circle"></i></span>
                                        <img src="<?php echo get_file_uri("assets/images/forecast_style_1.png") ?>" alt="style_1" />
                                    </div>
                                    <div data-value="style_2" class="item <?php echo $forecast_style === 'style_2' ? ' active ' : ''; ?>" >
                                        <span class="selected-mark <?php echo $forecast_style === 'style_2' ? '' : 'hide'; ?>"><i data-feather="check-circle"></i></span>
                                        <img src="<?php echo get_file_uri("assets/images/forecast_style_2.png") ?>" alt="style_2" />
                                    </div>

                                </div>    
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <label for="default_due_date_after_billing_date" class="col-md-2"><?php echo app_lang('default_due_date_after_billing_date'); ?></label>
                            <div class="col-md-3">
                                <?php
                                echo form_input(array(
                                    "id" => "default_due_date_after_billing_date",
                                    "name" => "default_due_date_after_billing_date",
                                    "type" => "number",
                                    "value" => get_setting("default_due_date_after_billing_date"),
                                    "class" => "form-control mini",
                                    "min" => 0
                                ));
                                ?>
                            </div>
                            <label class="col-md-1 mt5"><?php echo app_lang('days'); ?></label>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <label for="send_bcc_to" class=" col-md-2"><?php echo app_lang('send_bcc_to'); ?></label>
                            <div class=" col-md-10">
                                <?php
                                echo form_input(array(
                                    "id" => "send_bcc_to",
                                    "name" => "send_bcc_to",
                                    "value" => get_setting("send_bcc_to"),
                                    "class" => "form-control",
                                    "placeholder" => app_lang("email")
                                ));
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <label for="allow_partial_forecast_actuals_from_bu" class=" col-md-2"><?php echo app_lang('allow_partial_forecast_actuals_from_bu'); ?></label>

                            <div class="col-md-10">
                                <?php
                                echo form_dropdown(
                                        "allow_partial_forecast_actuals_from_bu", array("1" => app_lang("yes"), "0" => app_lang("no")), get_setting('allow_partial_forecast_actuals_from_bu'), "class='select2 mini'"
                                );
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <label for="send_forecast_due_pre_reminder" class=" col-md-2"><?php echo app_lang('send_due_forecast_reminder_notification_before'); ?> <span class="help" data-bs-toggle="tooltip" title="<?php echo app_lang('cron_job_required'); ?>"><i data-feather='help-circle' class="icon-16"></i></span></label>

                            <div class="col-md-3">
                                <?php
                                echo form_dropdown(
                                        "send_forecast_due_pre_reminder", array(
                                    "" => " - ",
                                    "1" => "1 " . app_lang("day"),
                                    "2" => "2 " . app_lang("days"),
                                    "3" => "3 " . app_lang("days"),
                                    "5" => "5 " . app_lang("days"),
                                    "7" => "7 " . app_lang("days"),
                                    "10" => "10 " . app_lang("days"),
                                    "14" => "14 " . app_lang("days"),
                                    "15" => "15 " . app_lang("days"),
                                    "20" => "20 " . app_lang("days"),
                                    "30" => "30 " . app_lang("days"),
                                        ), get_setting('send_forecast_due_pre_reminder'), "class='select2 mini'"
                                );
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <label for="send_forecast_due_after_reminder" class=" col-md-2"><?php echo app_lang('send_forecast_overdue_reminder_after'); ?> <span class="help" data-bs-toggle="tooltip" title="<?php echo app_lang('cron_job_required'); ?>"><i data-feather='help-circle' class="icon-16"></i></span></label>

                            <div class="col-md-3">
                                <?php
                                echo form_dropdown(
                                        "send_forecast_due_after_reminder", array(
                                    "" => " - ",
                                    "1" => "1 " . app_lang("day"),
                                    "2" => "2 " . app_lang("days"),
                                    "3" => "3 " . app_lang("days"),
                                    "5" => "5 " . app_lang("days"),
                                    "7" => "7 " . app_lang("days"),
                                    "10" => "10 " . app_lang("days"),
                                    "14" => "14 " . app_lang("days"),
                                    "15" => "15 " . app_lang("days"),
                                    "20" => "20 " . app_lang("days"),
                                    "30" => "30 " . app_lang("days"),
                                        ), get_setting('send_forecast_due_after_reminder'), "class='select2 mini'"
                                );
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <label for="send_recurring_forecast_reminder_before_creation" class=" col-md-2"><?php echo app_lang('send_recurring_forecast_reminder_before_creation'); ?> <span class="help" data-bs-toggle="tooltip" title="<?php echo app_lang('cron_job_required'); ?>"><i data-feather='help-circle' class="icon-16"></i></span></label>

                            <div class="col-md-3">
                                <?php
                                echo form_dropdown(
                                        "send_recurring_forecast_reminder_before_creation", array(
                                    "" => " - ",
                                    "1" => "1 " . app_lang("day"),
                                    "2" => "2 " . app_lang("days"),
                                    "3" => "3 " . app_lang("days"),
                                    "5" => "5 " . app_lang("days"),
                                    "7" => "7 " . app_lang("days"),
                                    "10" => "10 " . app_lang("days"),
                                    "14" => "14 " . app_lang("days"),
                                    "15" => "15 " . app_lang("days"),
                                    "20" => "20 " . app_lang("days"),
                                    "30" => "30 " . app_lang("days"),
                                        ), get_setting('send_recurring_forecast_reminder_before_creation'), "class='select2 mini'"
                                );
                                ?>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" id="last_forecast_id" name="last_forecast_id" value="<?php echo $last_id; ?>" />
                    <div class="form-group">
                        <div class="row">
                            <label for="initial_number_of_the_forecast" class="col-md-2"><?php echo app_lang('initial_number_of_the_forecast'); ?></label>
                            <div class="col-md-3">
                                <?php
                                echo form_input(array(
                                    "id" => "initial_number_of_the_forecast",
                                    "name" => "initial_number_of_the_forecast",
                                    "type" => "number",
                                    "value" => (get_setting("initial_number_of_the_forecast") > ($last_id + 1)) ? get_setting("initial_number_of_the_forecast") : ($last_id + 1),
                                    "class" => "form-control mini",
                                    "data-rule-greaterThan" => "#last_forecast_id",
                                    "data-msg-greaterThan" => app_lang("the_forecast_id_must_be_larger_then_last_forecast_id")
                                ));
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <label for="bu_can_pay_forecast_without_login" class=" col-md-2"><?php echo app_lang('bu_can_pay_forecast_without_login'); ?> <span class="help" data-bs-toggle="tooltip" title="<?php echo app_lang('bu_can_pay_forecast_without_login_help_message'); ?>"><i data-feather='help-circle' class="icon-16"></i></span></label>

                            <div class="col-md-10">
                                <?php
                                echo form_dropdown(
                                        "bu_can_pay_forecast_without_login", array("1" => app_lang("yes"), "0" => app_lang("no")), get_setting('bu_can_pay_forecast_without_login'), "class='select2 mini'"
                                );
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary"><span data-feather='check-circle' class="icon-16"></span> <?php echo app_lang('save'); ?></button>
                </div>
            </div>

            <?php echo form_close(); ?>
        </div>
    </div>
</div>
<?php echo view("includes/cropbox"); ?>

<?php
load_css(array(
    "assets/js/summernote/summernote.css"
));
load_js(array(
    "assets/js/summernote/summernote.min.js"
));
?>

<script type="text/javascript">
    $(document).ready(function () {
        $("#forecast-settings-form").appForm({
            isModal: false,
            beforeAjaxSubmit: function (data) {
                $.each(data, function (index, obj) {
                    if (obj.name === "forecast_footer") {
                        data[index]["value"] = encodeAjaxPostData(getWYSIWYGEditorHTML("#forecast_footer"));
                    }
                    if (obj.name === "forecast_logo") {
                        var image = replaceAll(":", "~", data[index]["value"]);
                        data[index]["value"] = image;
                    }
                });
            },
            onSuccess: function (result) {
                if (result.success) {
                    appAlert.success(result.message, {duration: 10000});
                } else {
                    appAlert.error(result.message);
                }

                if ($("#forecast_logo").val() || result.reload_page) {
                    location.reload();
                }
            }
        });
        $("#forecast-settings-form .select2").select2();

        initWYSIWYGEditor("#forecast_footer", {height: 100});

        $(".cropbox-upload").change(function () {
            showCropBox(this);
        });

        $(".forecast-styles .item").click(function () {
            $(".forecast-styles .item").removeClass("active");
            $(".forecast-styles .item .selected-mark").addClass("hide");
            $(this).addClass("active");
            $(this).find(".selected-mark").removeClass("hide");
            $("#forecast_style").val($(this).attr("data-value"));
        });

        $('[data-bs-toggle="tooltip"]').tooltip();
    });
</script>