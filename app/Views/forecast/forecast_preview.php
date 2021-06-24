<div id="page-content" class="page-wrapper clearfix">
    <?php
    load_css(array(
        "assets/css/forecast.css",
    ));
    ?>

    <div class="forecast-preview">
        <?php if ($login_user->user_type === "bu" && $forecast_total_summary->balance_due >= 1) { ?>
            <div class="card d-block p15 no-border clearfix">
                <div class="inline-block strong float-start pt5 pr15">
                    <?php echo app_lang("pay_forecast"); ?>:
                </div>
                <div class="mr15 strong float-start general-form" style="width: 145px;" >
                    <?php if (get_setting("allow_partial_forecast_actuals_from_bu")) { ?>
                        <span class="forecast-actuals-amount-section" style="background-color: #f6f8f9; display: inline-block; padding: 8px 2px 7px 10px;"><?php echo $forecast_total_summary->currency; ?></span><input type="text" id="actuals-amount" value="<?php echo to_decimal_format($forecast_total_summary->balance_due); ?>" class="form-control inline-block fw-bold" style="padding-left: 3px; width: 100px" />
                    <?php } else { ?>
                        <span class="pt5 inline-block">
                            <?php echo to_currency($forecast_total_summary->balance_due, $forecast_total_summary->currency . " "); ?>
                        </span>
                    <?php } ?>
                </div>

                <div class="float-end">
                    <?php
                    echo "<div class='text-center'>" . anchor("forecast/download_pdf/" . $forecast_info->id, app_lang("download_pdf"), array("class" => "btn btn-default round")) . "</div>"
                    ?>
                </div>

            </div>
            <?php
        } else if ($login_user->user_type === "bu") {
            echo "<div class='text-center'>" . anchor("forecast/download_pdf/" . $forecast_info->id, app_lang("download_pdf"), array("class" => "btn btn-default round")) . "</div>";
        }


        if ($show_close_preview) {
            echo "<div class='text-center'>" . anchor("forecast/view/" . $forecast_info->id, app_lang("close_preview"), array("class" => "btn btn-default round")) . "</div>";
        }
        ?>

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



</script>
