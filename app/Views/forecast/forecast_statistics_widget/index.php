<div id="forecast-actuals-statistics-container">
    <?php echo view("forecast/forecast_statistics_widget/widget_data"); ?>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $(".load-currency-wise-data").click(function () {
            var currencyValue = $(this).attr("data-value");

            $.ajax({
                url: "<?php echo get_uri('forecast/load_statistics_of_selected_currency') ?>" + "/" + currencyValue,
                type: 'POST',
                dataType: 'json',
                success: function (result) {
                    if (result.success) {
                        $("#forecast-actuals-statistics-container").html(result.statistics);
                    }
                }
            });
        });
    });
</script>    

