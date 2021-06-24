<div id="page-content" class="clearfix">
    <?php
    load_css(array(
        "assets/css/forecast.css",
    ));
    ?>

    <div class="forecast-preview print-forecast">
        <div class="forecast-preview-container bg-white mt15">
            <div class="row">
                <div class="col-md-12 position-relative">
                    <div class="ribbon"><?php echo $forecast_status_label; ?></div>
                </div>
            </div>

            <?php echo $forecast_preview; ?>
        </div>

    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $("html, body").addClass("dt-print-view");
    });
</script>