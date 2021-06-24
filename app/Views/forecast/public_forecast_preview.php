<div id="page-content" class="page-wrapper clearfix public-forecast-preview">
    <?php
    echo view('includes/head');

    load_css(array(
        "assets/css/forecast.css",
    ));
    ?>

    <div class="forecast-preview">
        <?php if ($forecast_total_summary->balance_due >= 1 && count($actuals_methods) && !$bu_info->disable_online_actuals) { ?>
            <div class="card d-block p15 no-border clearfix">
                <div class="inline-block strong float-start pt5 pr15">
                    <?php echo app_lang("pay_forecast"); ?>:
                </div>
                <div class="mr15 strong float-start general-form" style="width: 145px;" >
                    <?php if (get_setting("allow_partial_forecast_actuals_from_bu")) { ?>
                        <span class="forecast-actuals-amount-section" style="background-color: #f6f8f9; display: inline-block; padding: 7px 2px 7px 10px;"><?php echo $forecast_total_summary->currency; ?></span><input type="text" id="actuals-amount" value="<?php echo to_decimal_format($forecast_total_summary->balance_due); ?>" class="form-control inline-block" style="padding-left: 3px; width: 100px" />
                    <?php } else { ?>
                        <span class="pt5 inline-block">
                            <?php echo to_currency($forecast_total_summary->balance_due, $forecast_total_summary->currency . " "); ?>
                        </span>
                    <?php } ?>
                </div>

                <?php
                foreach ($actuals_methods as $actuals_method) {

                    $method_type = get_array_value($actuals_method, "type");

                    $pass_variables = array(
                        "actuals_method" => $actuals_method,
                        "balance_due" => $forecast_total_summary->balance_due,
                        "currency" => $forecast_total_summary->currency,
                        "forecast_info" => $forecast_info,
                        "forecast_id" => $forecast_id,
                        "contact_user_id" => $contact_id);
                }
                ?>
            </div>
        <?php } ?>

        <div class="forecast-preview-container bg-white mt15">
            <div class="row">
                <div class="col-md-12 position-relative">
                    <div class="ribbon"><?php echo $forecast_status_label; ?></div>
                </div>
            </div>

            <?php
            echo $forecast_preview;
            ?>
        </div>

    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $("#actuals-amount").change(function () {
            var value = $(this).val();
            $(".actuals-amount-field").each(function () {
                $(this).val(value);
            });
        });
    });

    $("html, body").css({"overflow-y": "auto"});

</script>

