<div class="card bg-white <?php echo $custom_class; ?>">
    <div class="card-header clearfix">
        <i data-feather="bar-chart" class="icon-16"></i>&nbsp;<?php echo app_lang("forecast_vs_actuals"); ?>

        <span class="float-end" data-bs-toggle="tooltip" title="<?php echo app_lang('forecast_actuals_widget_help_message') ?>"><i data-feather="help-circle" class="icon-16"></i></span>
    </div>
    <div class="card-body rounded-bottom">
        <canvas id="forecast-actuals-chart" style="width: 100%; height: 255px;"></canvas>
    </div>
</div>

<script type="text/javascript">

<?php if ($forecast || $actuals) { ?>
        var forecastActualsChart = document.getElementById("forecast-actuals-chart");
        new Chart(forecastActualsChart, {
            type: 'doughnut',
            data: {
                labels: ["<?php echo app_lang("forecast"); ?>", "<?php echo app_lang("actuals"); ?>"],
                datasets: [
                    {
                        data: ["<?php echo $forecast ?>" * 1, "<?php echo $actuals ?>" * 1],
                        backgroundColor: ["#00B393", "#F06C71"],
                        borderWidth: 0
                    }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                tooltips: {
                    callbacks: {
                        title: function (tooltipItem, data) {
                            return data['labels'][tooltipItem[0]['index']];
                        },
                        label: function (tooltipItem, data) {
                            return "";
                        },
                        afterLabel: function (tooltipItem, data) {
                            var dataset = data['datasets'][0];
                            var percent = Math.round((dataset['data'][tooltipItem['index']] / dataset["_meta"][Object.keys(dataset["_meta"])[0]]['total']) * 100);
                            return '(' + percent + '%)';
                        }
                    }
                },
                legend: {
                    display: true,
                    position: 'bottom',
                    labels: {
                        fontColor: "#898fa9"
                    }
                },
                animation: {
                    animateScale: true
                }
            }
        });
<?php } ?>

    $(document).ready(function () {
        $('[data-bs-toggle="tooltip"]').tooltip();
    });
</script>