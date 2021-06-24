<div class="card clearfix">
    <div class="table-responsive">
        <table id="sub-forecast-table" class="display" cellspacing="0" width="100%">   
        </table>
    </div>

</div>

<script type="text/javascript">

    $(document).ready(function () {

        $("#sub-forecast-table").appTable({
            source: '<?php echo_uri("forecast/sub_forecast_list_data/" . $recurring_forecast_id) ?>',
            order: [[0, "desc"]],
            columns: [
                {title: "<?php echo app_lang("forecast_id") ?>", "class": "w10p"},
                {visible: false},
                {visible: false},
                {visible: false, searchable: false},
                {title: "<?php echo app_lang("forecast_date") ?>", "class": "w10p", "iDataSort": 3},
                {visible: false, searchable: false},
                {title: "<?php echo app_lang("due_date") ?>", "class": "w10p", "iDataSort": 5},
                {title: "<?php echo app_lang("forecast_value") ?>", "class": "w10p text-right"},
                {title: "<?php echo app_lang("actuals_achieved") ?>", "class": "w10p text-right"},
                {title: "<?php echo app_lang("status") ?>", "class": "w10p text-center"}
            ],
            summation: [{column: 7, dataType: 'currency', currencySymbol: "none"}, {column: 8, dataType: 'currency', currencySymbol: "none"}]
        });

    });
</script>