<div class="card">
    <div class="card-header clearfix border-bottom-0">
        <div class="float-start strong"><i data-feather="bar-chart-2" class="icon-16"></i>&nbsp; <?php echo app_lang("chart"); ?></div>
        <div class="float-end">
            <div id="actuals-chart-date-range-selector">

            </div>
        </div>
    </div>
    <div class="card-body ">
        <canvas id="yearly-actuals-chart" style="width:100%; height: 350px;"></canvas>
    </div>
</div>


<script type="text/javascript">
    var prepareActualsChart = function (data) {
        appLoader.show();
        $.ajax({
            url: "<?php echo_uri("actualss/yearly_chart_data") ?>",
            data: data,
            cache: false,
            type: 'POST',
            dataType: "json",
            success: function (response) {
                appLoader.hide();
                initActualsChart(response.months, response.data);
            }
        });

    };

    var yearlyActualsChartContent;

    var initActualsChart = function (months, data) {
        // var months = ﻿["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
        // var data = ["1000", "0", "1200", "0", "600", "500", "0", "0", "0", "1700", "0", "0"],

        var yearlyActualsChart = document.getElementById("yearly-actuals-chart");

        if (yearlyActualsChartContent) {
            yearlyActualsChartContent.destroy();
        }

        yearlyActualsChartContent = new Chart(yearlyActualsChart, {
            type: 'bar',
            data: {
                labels: months,
                datasets: [{
                        label: '',
                        data: data,
                        borderColor: '#ff8c1a',
                        backgroundColor: 'rgba(255, 205, 86, 0.2)',
                        borderWidth: 1
                    }]},
            options: {
                responsive: true,
                tooltips: {
                    callbacks: {
                        label: function (tooltipItem, data) {
                            return toCurrency((data['datasets'][0]['data'][tooltipItem['index']]));
                        }
                    }
                },
                legend: {
                    display: true,
                    position: 'bottom'
                },
                scales: {
                    xAxes: [{
                            gridLines: {
                                color: 'rgba(127,127,127,0.1)'
                            },
                            ticks: {
                                fontColor: "#898fa9"
                            }
                        }],
                    yAxes: [{
                            gridLines: {
                                color: 'rgba(127,127,127,0.1)'
                            },
                            ticks: {
                                fontColor: "#898fa9"
                            }
                        }]
                }
            }
        });
    };

    $(document).ready(function () {
        $("#actuals-chart-date-range-selector").appDateRange({
            dateRangeType: "yearly",
            onChange: function (dateRange) {
                prepareActualsChart(dateRange);
            },
            onInit: function (dateRange) {
                prepareActualsChart(dateRange);
            }
        });

    });
</script>