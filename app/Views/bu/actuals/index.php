<div class="card rounded-0">
    <div class="tab-title clearfix">
        <h4><?php echo app_lang('actuals'); ?></h4>
        <div class="title-button-group">
            <?php echo modal_anchor(get_uri("actuals/modal_form"), "<i data-feather='plus-circle' class='icon-16'></i>" . app_lang('add_actuals'), array("class" => "btn btn-default", "data-post-bu_id" => $bu_id, "title" => app_lang('add_actuals'))); ?>
        </div>
    </div>
    <div class="table-responsive">
        <table id="actuals-table" class="display" width="100%">
        </table>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        $("#actuals-table").appTable({
            source: '<?php echo_uri("actuals/actuals_list_data_of_bu/" . $bu_id) ?>',
            order: [[0, "desc"]],
            columns: [
                {visible: false, searchable: false},
                {title: '<?php echo app_lang("date") ?>', "iDataSort": 0},
                {title: '<?php echo app_lang("category") ?>'},
                {title: '<?php echo app_lang("title") ?>'},
                {title: '<?php echo app_lang("description") ?>'},
                {title: '<?php echo app_lang("files") ?>'},
                {title: '<?php echo app_lang("amount") ?>', "class": "text-right"},
                {title: '<?php echo app_lang("total") ?>', "class": "text-right"},
                {visible: false, searchable: false}
            ],
            summation: [{column: 6, dataType: 'currency'}, {column: 7, dataType: 'currency'}, {column: 8, dataType: 'currency'}, {column: 9, dataType: 'currency'}]
        });
    });
</script>


<?php if (isset($page_type) && $page_type === "full") { ?>
    <div id="page-content" class="page-wrapper clearfix">
    <?php } ?>

    <div class="card rounded-0">
        <?php if (isset($page_type) && $page_type === "full") { ?>
            <div class="page-title clearfix">
                <h1><?php echo app_lang('actuals'); ?></h1>
            </div>
        <?php } else { ?>
            <div class="tab-title clearfix">
                <h4><?php echo app_lang('actuals'); ?></h4>
            </div>
        <?php } ?>

        <div class="table-responsive">
            <table id="forecast-actuals-table" class="display" width="100%">
            </table>
        </div>
    </div>
    <?php if (isset($page_type) && $page_type === "full") { ?>
    </div>
<?php } ?>

<script type="text/javascript">
    $(document).ready(function () {
        var currencySymbol = "<?php echo $bu_info->currency_symbol; ?>";
        $("#forecast-actuals-table").appTable({
            source: '<?php echo_uri("forecast_actuals/actuals_list_data_of_bu/" . $bu_id) ?>',
            order: [[1, "desc"]],
            columns: [
                {title: '<?php echo app_lang("forecast_id") ?> ', "class": "w10p"},
                {visible: false, searchable: false},
                {title: '<?php echo app_lang("actuals_date") ?> ', "class": "w15p", "iDataSort": 1},
                {title: '<?php echo app_lang("note") ?>'},
                {title: '<?php echo app_lang("amount") ?>', "class": "text-right w15p"}
            ],
            printColumns: [0, 1, 2, 3, 4],
            xlsColumns: [0, 1, 2, 3, 4],
            summation: [{column: 5, dataType: 'currency', currencySymbol: currencySymbol}]
        });

    });
</script>