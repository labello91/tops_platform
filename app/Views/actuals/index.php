<div id="page-content" class="page-wrapper clearfix">
    <div class="card clearfix">
        <ul id="actuals-tabs" data-bs-toggle="ajax-tab" class="nav nav-tabs bg-white title" role="tablist">
            <li class="title-tab"><h4 class="pl15 pt10 pr15"><?php echo app_lang("actuals"); ?></h4></li>
            <li><a id="monthly-actuals-button"  role="presentation"  href="javascript:;" data-bs-target="#monthly-actuals"><?php echo app_lang("monthly"); ?></a></li>
            <li><a role="presentation" href="<?php echo_uri("actuals/yearly/"); ?>" data-bs-target="#yearly-actuals"><?php echo app_lang('yearly'); ?></a></li>
            <li><a role="presentation" href="<?php echo_uri("actuals/custom/"); ?>" data-bs-target="#custom-actuals"><?php echo app_lang('custom'); ?></a></li>
            <li><a role="presentation" href="<?php echo_uri("actuals/recurring/"); ?>" data-bs-target="#recurring-actuals"><?php echo app_lang('recurring'); ?></a></li>
            <li><a role="presentation" href="<?php echo_uri("actuals/yearly_chart/"); ?>" data-bs-target="#yearly-chart"><?php echo app_lang('chart'); ?></a></li>
            <div class="tab-title clearfix no-border">
                <div class="title-button-group">
                    <?php echo modal_anchor(get_uri("actuals/modal_form"), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('add_actuals'), array("class" => "btn btn-default mb0", "title" => app_lang('add_actuals'))); ?>
                </div>
            </div>
        </ul>

        <div class="tab-content">
            <div role="tabpanel" class="tab-pane fade" id="monthly-actuals">
                <div class="table-responsive">
                    <table id="monthly-actuals-table" class="display" cellspacing="0" width="100%">
                    </table>
                </div>
            </div>
            <div role="tabpanel" class="tab-pane fade" id="yearly-actuals"></div>
            <div role="tabpanel" class="tab-pane fade" id="custom-actuals"></div>
            <div role="tabpanel" class="tab-pane fade" id="recurring-actuals"></div>
            <div role="tabpanel" class="tab-pane fade" id="yearly-chart"></div>
        </div>
    </div>
</div>

<script type="text/javascript">
    loadActualsTable = function (selector, dateRange) {
    var customDatePicker = "", recurring = "0";
    if (dateRange === "custom" || dateRange === "recurring") {
    customDatePicker = [{startDate: {name: "start_date", value: moment().format("YYYY-MM-DD")}, endDate: {name: "end_date", value: moment().format("YYYY-MM-DD")}, showClearButton: true}];
    if (dateRange === "recurring"){
    recurring = "1";
    }

    dateRange = "";
    }

    $(selector).appTable({
    source: '<?php echo_uri("actuals/list_data") ?>/' + recurring,
            dateRangeType: dateRange,
            filterDropdown: [
            {name: "category_id", class: "w200", options: <?php echo $categories_dropdown; ?>},
            {name: "user_id", class: "w200", options: <?php echo $members_dropdown; ?>},
<?php if ($projects_dropdown) { ?>
                {name: "project_id", class: "w200", options: <?php echo $projects_dropdown; ?>}
<?php } ?>
            ],
            order: [[0, "asc"]],
            rangeDatepicker: customDatePicker,
            columns: [
            {visible: false, searchable: false},
            {title: '<?php echo app_lang("date") ?>', "iDataSort": 0},
            {title: '<?php echo app_lang("category") ?>'},
            {title: '<?php echo app_lang("title") ?>'},
            {title: '<?php echo app_lang("description") ?>'},
            {title: '<?php echo app_lang("files") ?>'},
            {title: '<?php echo app_lang("amount") ?>', "class": "text-right"},
            {title: '<?php echo app_lang("total") ?>', "class": "text-right"}
<?php echo $custom_field_headers; ?>,
            {title: '<i data-feather="menu" class="icon-16"></i>', "class": "text-center option w100"}
            ],
            printColumns: [1, 2, 3, 4, 6, 7, 8, 9],
            xlsColumns: [1, 2, 3, 4, 6, 7, 8, 9],
            summation: [{column: 6, dataType: 'currency'}, {column: 7, dataType: 'currency'}, {column: 8, dataType: 'currency'}, {column: 9, dataType: 'currency'}]
    });
    };
    $(document).ready(function () {
    loadActualsTable("#monthly-actuals-table", "monthly");
    });
</script>
