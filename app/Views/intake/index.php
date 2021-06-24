<div id="page-content" class="page-wrapper clearfix">
    <ul class="nav nav-tabs bg-white title" role="tablist">
        <li class="title-tab"><h4 class="pl15 pt10 pr15"><?php echo app_lang("intake"); ?></h4></li>

        <?php echo view("intake/tabs", array("active_tab" => "intake_list")); ?>

        <div class="tab-title clearfix no-border">
            <div class="title-button-group">
                <?php echo modal_anchor(get_uri("intake/import_intake_modal_form"), "<i data-feather='upload' class='icon-16'></i> " . app_lang('import_intake'), array("class" => "btn btn-default", "title" => app_lang('import_intake'))); ?>
                <?php echo modal_anchor(get_uri("intake/modal_form"), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('add_intake'), array("class" => "btn btn-default", "title" => app_lang('add_intake'))); ?>
            </div>
        </div>
    </ul>

    <div class="card">
        <div class="table-responsive">
            <table id="intake-table" class="display" cellspacing="0" width="100%">            
            </table>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {

    $("#intake-table").appTable({
    source: '<?php echo_uri("intake/list_data") ?>',
            columns: [
            {title: "<?php echo app_lang("bu_name") ?>"},
            {title: "<?php echo app_lang("primary_contact") ?>"},
            {title: "<?php echo app_lang("owner") ?>"},
            {title: "<?php echo app_lang("status") ?>"}
<?php echo $custom_field_headers; ?>,
            {title: '<i data-feather="menu" class="icon-16"></i>', "class": "text-center option w100"}
            ],
            filterDropdown: [
            {name: "status", class: "w200", options: <?php echo view("intake/intake_statuses"); ?>},
            {name: "source", class: "w200", options: <?php echo view("intake/intake_sources"); ?>},
<?php if (get_array_value($login_user->permissions, "intake") !== "own") { ?>
                {name: "owner_id", class: "w200", options: <?php echo json_encode($owners_dropdown); ?>}
<?php } ?>
            ],
            printColumns: combineCustomFieldsColumns([0, 1, 2], '<?php echo $custom_field_headers; ?>'),
            xlsColumns: combineCustomFieldsColumns([0, 1, 2], '<?php echo $custom_field_headers; ?>')
    });
    }
    );
</script>

<?php echo view("intake/update_intake_status_script"); ?>