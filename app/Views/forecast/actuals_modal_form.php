<?php echo form_open(get_uri("forecast_actuals/save_actuals"), array("id" => "forecast-actuals-form", "class" => "general-form", "role" => "form")); ?>
<div class="modal-body clearfix">
    <div class="container-fluid">
        <input type="hidden" name="id" value="<?php echo $model_info->id; ?>" />

        <?php if ($forecast_id) { ?>
            <input type="hidden" name="forecast_id" value="<?php echo $forecast_id; ?>" />
        <?php } else { ?>
            <div class="form-group">
                <div class="row">
                    <label for="forecast-actuals_id" class=" col-md-3"><?php echo app_lang('forecast'); ?></label>
                    <div class="col-md-9">
                        <?php
                        echo form_dropdown("forecast_id", $forecasts_dropdown, "", "class='select2 validate-hidden' id='forecast_id' data-rule-required='true' data-msg-required='" . app_lang('field_required') . "' ");
                        ?>
                    </div>
                </div>
            </div>
        <?php } ?>

        <div class="form-group">
            <div class="row">
                <label for="forecast_actuals_method_id" class=" col-md-3"><?php echo app_lang('actuals_updated'); ?></label>
                <div class="col-md-9">
                    <?php
                    echo form_dropdown("forecast_actuals_method_id", $actuals_updated_dropdown, array($model_info->actuals_updated_id), "class='select2'");
                    ?>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="row">
                <label for="forecast_actuals_date" class=" col-md-3"><?php echo app_lang('actuals_date'); ?></label>
                <div class="col-md-9">
                    <?php
                    echo form_input(array(
                        "id" => "forecast_actuals_date",
                        "name" => "forecast_actuals_date",
                        "value" => $model_info->actuals_date,
                        "class" => "form-control",
                        "placeholder" => app_lang('actuals_date'),
                        "autocomplete" => "off",
                        "data-rule-required" => true,
                        "data-msg-required" => app_lang("field_required")
                    ));
                    ?>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="row">
                <label for="forecast_actuals_amount" class=" col-md-3"><?php echo app_lang('amount'); ?></label>
                <div class="col-md-9">
                    <?php
                    echo form_input(array(
                        "id" => "forecast_actuals_amount",
                        "name" => "forecast_actuals_amount",
                        "value" => $model_info->amount ? to_decimal_format($model_info->amount) : "",
                        "class" => "form-control",
                        "placeholder" => app_lang('amount'),
                        "data-rule-required" => true,
                        "data-msg-required" => app_lang("field_required"),
                    ));
                    ?>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="row">
                <label for="forecast_actuals_note" class="col-md-3"><?php echo app_lang('note'); ?></label>
                <div class=" col-md-9">
                    <?php
                    echo form_textarea(array(
                        "id" => "forecast_actuals_note",
                        "name" => "forecast_actuals_note",
                        "value" => $model_info->note ? $model_info->note : "",
                        "class" => "form-control",
                        "placeholder" => app_lang('description'),
                        "data-rich-text-editor" => true
                    ));
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-default" data-bs-dismiss="modal"><span data-feather="x" class="icon-16"></span> <?php echo app_lang('close'); ?></button>
    <button type="submit" class="btn btn-primary"><span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang('save'); ?></button>
</div>
<?php echo form_close(); ?>

<script type="text/javascript">
    $(document).ready(function () {
        $("#forecast-actuals-form").appForm({
            onSuccess: function (result) {
                if (typeof RELOAD_VIEW_AFTER_UPDATE !== "undefined" && RELOAD_VIEW_AFTER_UPDATE) {
                    location.reload();
                } else {
                    if ($("#forecast-actuals-table").length) {
                        //it's from forecast details view
                        $("#forecast-actuals-table").appTable({newData: result.data, dataId: result.id});
                        $("#forecast-total-section").html(result.forecast_total_view);
                        if (typeof updateForecastStatusBar == 'function') {
                            updateForecastStatusBar(result.forecast_id);
                        }
                    } else {
                        //it's from forecasts list view
                        //update table data
                        $("#" + $(".dataTable:visible").attr("id")).appTable({reload: true});
                    }
                }
            }
        });
        $("#forecast-actuals-form .select2").select2();

        setDatePicker("#forecast_actuals_date");

    });
</script>