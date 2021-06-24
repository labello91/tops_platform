<div class="card">
    <div class="tab-title clearfix">
        <h4><?php echo app_lang('actuals'); ?></h4>
    </div>

    <div class="table-responsive">
        <table id="forecast-actuals-table" class="display" width="100%">
        </table>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        var currencySymbol = "<?php echo $project_info->currency_symbol; ?>";
        $("#forecast-actuals-table").appTable({
            source: '<?php echo_uri("forecast_actuals/actuals_list_data_of_project/" . $project_id) ?>',
            order: [[0, "asc"]],
            columns: [
                {title: '<?php echo app_lang("forecast_id") ?> ', "class": "w10p"},
                {visible: false, searchable: false},
                {title: '<?php echo app_lang("actuals_date") ?> ', "class": "w15p", "iDataSort": 1},
                {title: '<?php echo app_lang("actuals_method") ?>', "class": "w15p"},
                {title: '<?php echo app_lang("note") ?>'},
                {title: '<?php echo app_lang("file") ?>', "class": "w15p"},
                {title: '<?php echo app_lang("amount") ?>', "class": "text-right w15p"}
            ],
            printColumns: [0, 1, 2, 3, 4],
            xlsColumns: [0, 1, 2, 3, 4],
            summation: [{column: 5, dataType: 'currency', currencySymbol: currencySymbol}]
        });

    });
</script>