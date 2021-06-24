<div class="table-responsive">
    <table id="forecast-vs-actuals-summary-table" class="display" cellspacing="0" width="100%">
    </table>
</div>

<script>
    $("#forecast-vs-actuals-summary-table").appTable({
    source: '<?php echo_uri("actuals/forecast_vs_actuals_summary_list_data"); ?>',
            order: [[0, "desc"]],
            dateRangeType: "yearly",
            filterDropdown: [
<?php if ($projects_dropdown) { ?>
                {name: "project_id", class: "w200", options: <?php echo $projects_dropdown; ?>}
<?php } ?>
            ],
            columns: [
            {visible: false, searchable: false}, //sorting purpose only
            {title: '<?php echo app_lang("month") ?>', "class": "w30p", "iDataSort": 0},
            {title: '<?php echo app_lang("forecast") ?>', "class": "w20p text-right"},
            {title: '<?php echo app_lang("actuals") ?>', "class": "w20p text-right"},
            {title: '<?php echo app_lang("profit") ?>', "class": "w20p text-right"}
            ],
            printColumns: [1, 2, 3, 4],
    xlsColumns: [1, 2, 3, 4],
            summation: [{column:2, dataType: 'currency'}, {column:3, dataType: 'currency'}, {column:4, dataType: 'currency'}]
    }
    );
</script>