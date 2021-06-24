<div class="card rounded-0">
    <div class="tab-title clearfix">
        <h4><?php echo app_lang('actuals'); ?></h4>
        <div class="title-button-group">
            <?php echo modal_anchor(get_uri("actuals/modal_form"), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('add_actuals'), array("class" => "btn btn-default mb0", "title" => app_lang('add_actuals'), "data-post-user_id" => $user_id)); ?>
        </div>
    </div>
    <div class="table-responsive">
        <table id="actuals-table" class="display" cellspacing="0" width="100%">
        </table>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        $EXPENSE_TABLE = $("#actuals-table");

        $EXPENSE_TABLE.appTable({
            source: '<?php echo_uri("actuals/list_data/") ?>',
            filterParams: {user_id: "<?php echo $user_id; ?>"},
            order: [[0, "asc"]],
            columns: [
                {visible: false, searchable: false},
                {title: '<?php echo app_lang("date") ?>', "iDataSort": 0},
                {title: '<?php echo app_lang("category") ?>'},
                {title: '<?php echo app_lang("title") ?>'},
                {title: '<?php echo app_lang("description") ?>'},
                {title: '<?php echo app_lang("file") ?>'},
                {title: '<?php echo app_lang("amount") ?>', "class": "text-right"},
                {title: '<?php echo app_lang("total") ?>', "class": "text-right"},
                {title: '<i data-feather="menu" class="icon-16"></i>', "class": "text-center option w100"}
            ],
            printColumns: [1, 2, 3, 4, 6, 7, 8, 9],
            xlsColumns: [1, 2, 3, 4, 6, 7, 8, 9],
            summation: [{column: 6, dataType: 'currency'}, {column: 7, dataType: 'currency'}, {column: 8, dataType: 'currency'}, {column: 9, dataType: 'currency'}]
        });
    });
</script>