<div id="page-content" class="page-wrapper clearfix">
    <div class="card clearfix">
        <ul id="forecast-tabs" data-bs-toggle="ajax-tab" class="nav nav-tabs bg-white title" role="tablist">
            <li class="title-tab"><h4 class="pl15 pt10 pr15"><?php echo app_lang("forecast"); ?></h4></li>
            <li><a id="monthly-expenses-button"  role="presentation"  href="javascript:;" data-bs-target="#monthly-forecast"><?php echo app_lang("monthly"); ?></a></li>
            <li><a role="presentation" href="<?php echo_uri("forecast/yearly/"); ?>" data-bs-target="#yearly-forecast"><?php echo app_lang('yearly'); ?></a></li>
            <li><a role="presentation" href="<?php echo_uri("forecast/custom/"); ?>" data-bs-target="#custom-forecast"><?php echo app_lang('custom'); ?></a></li>
            <li><a role="presentation" href="<?php echo_uri("forecast/recurring/"); ?>" data-bs-target="#recurring-forecast"><?php echo app_lang('recurring'); ?></a></li>
            <div class="tab-title clearfix no-border">
                <div class="title-button-group">
                    <?php if ($can_edit_forecast) { ?>
                        <?php echo modal_anchor(get_uri("labels/modal_form"), "<i data-feather='tag' class='icon-16'></i> " . app_lang('manage_labels'), array("class" => "btn btn-default mb0", "title" => app_lang('manage_labels'), "data-post-type" => "forecast")); ?>
                        <?php echo modal_anchor(get_uri("forecast_actuals/actuals_modal_form"), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('add_actuals'), array("class" => "btn btn-default mb0", "title" => app_lang('add_actuals'))); ?>
                        <?php echo modal_anchor(get_uri("forecast/modal_form"), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('add_forecast'), array("class" => "btn btn-default mb0", "title" => app_lang('add_forecast'))); ?>
                    <?php } ?>
                </div>
            </div>
        </ul>

        <div class="tab-content">
            <div role="tabpanel" class="tab-pane fade" id="monthly-forecast">
                <div class="table-responsive">
                    <table id="monthly-forecast-table" class="display" cellspacing="0" width="100%">   
                    </table>
                </div>
            </div>
            <div role="tabpanel" class="tab-pane fade" id="yearly-forecast"></div>
            <div role="tabpanel" class="tab-pane fade" id="custom-forecast"></div>
            <div role="tabpanel" class="tab-pane fade" id="recurring-forecast"></div>
        </div>
    </div>
</div>

<script type="text/javascript">
    loadForecastsTable = function (selector, dateRange) {
    var customDatePicker = "";
    if (dateRange === "custom") {
    customDatePicker = [{startDate: {name: "start_date", value: moment().format("YYYY-MM-DD")}, endDate: {name: "end_date", value: moment().format("YYYY-MM-DD")}, showClearButton: true}];
    dateRange = "";
    }

    var optionVisibility = false;
    if ("<?php echo $can_edit_forecast ?>") {
    optionVisibility = true;
    }

    $(selector).appTable({
    source: '<?php echo_uri("forecast/list_data") ?>',
            dateRangeType: dateRange,
            order: [[0, "desc"]],
            filterDropdown: [
            {name: "status", class: "w150", options: <?php echo view("forecast/forecast_statuses_dropdown"); ?>},
<?php if ($currencies_dropdown) { ?>
                {name: "currency", class: "w150", options: <?php echo $currencies_dropdown; ?>}
<?php } ?>
            ],
            rangeDatepicker: customDatePicker,
            columns: [
            {title: "<?php echo app_lang("forecast_id") ?>", "class": "w10p"},
            {title: "<?php echo app_lang("bu") ?>", "class": ""},
            {title: "<?php echo app_lang("project") ?>", "class": "w15p"},
            {visible: false, searchable: false},
            {title: "<?php echo app_lang("forecast_date") ?>", "class": "w10p", "iDataSort": 3},
            {visible: false, searchable: false},
            {title: "<?php echo app_lang("due_date") ?>", "class": "w10p", "iDataSort": 5},
            {title: "<?php echo app_lang("forecast_value") ?>", "class": "w10p text-right"},
            {title: "<?php echo app_lang("actuals_updated") ?>", "class": "w10p text-right"},
            {title: "<?php echo app_lang("status") ?>", "class": "w10p text-center"}
<?php echo $custom_field_headers; ?>,
            {title: '<i data-feather="menu" class="icon-16"></i>', "class": "text-center dropdown-option w100", visible: optionVisibility}
            ],
            printColumns: combineCustomFieldsColumns([0, 1, 2, 3, 5, 7, 8, 9], '<?php echo $custom_field_headers; ?>'),
            xlsColumns: combineCustomFieldsColumns([0, 1, 2, 3, 5, 7, 8, 9], '<?php echo $custom_field_headers; ?>'),
            summation: [{column: 7, dataType: 'number'}, {column: 8, dataType: 'number'}]
    });
    };
    $(document).ready(function () {
    loadForecastsTable("#monthly-forecast-table", "monthly");
    });
</script>