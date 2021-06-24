<div id="page-content" class="page-wrapper clearfix">
    <div class="card clearfix">
        <ul id="bu-tabs" data-bs-toggle="ajax-tab" class="nav nav-tabs bg-white title" role="tablist">
            <li><a id="bu-button" role="presentation" href="javascript:;" data-bs-target="#bu"><?php echo app_lang('bu'); ?></a></li>
            <li><a role="presentation" href="<?php echo_uri("bu/contacts/"); ?>" data-bs-target="#contacts"><?php echo app_lang('contacts'); ?></a></li>
            <div class="tab-title clearfix no-border">
                <div class="title-button-group">
                    <?php if ($can_edit_bu) { ?>
                        <?php echo modal_anchor(get_uri("bu/import_bu_modal_form"), "<i data-feather='upload' class='icon-16'></i> " . app_lang('import_bu'), array("class" => "btn btn-default", "title" => app_lang('import_bu'))); ?>
                        <?php echo modal_anchor(get_uri("bu/modal_form"), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('add_bu'), array("class" => "btn btn-default", "title" => app_lang('add_bu'))); ?>
                    <?php } ?>
                </div>
            </div>
        </ul>
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane fade" id="bu">
                <div class="table-responsive">
                    <table id="bu-table" class="display" cellspacing="0" width="100%">            
                    </table>
                </div>
            </div>
            <div role="tabpanel" class="tab-pane fade" id="contacts"></div>
        </div>
    </div>
</div>

<script type="text/javascript">
    loadClientsTable = function (selector) {
        var showForecastInfo = true;
        if (!"<?php echo $show_forecast_info; ?>") {
            showForecastInfo = false;
        }
        
        var showOptions = true;
        if (!"<?php echo $can_edit_bu; ?>") {
            showOptions = false;
        }

        $(selector).appTable({
            source: '<?php echo_uri("bu/list_data") ?>',
            filterDropdown: [
                {name: "group_id", class: "w200", options: <?php echo $groups_dropdown; ?>},
                {name: "quick_filter", class: "w200", options: <?php echo view("bu/quick_filters_dropdown"); ?>},
                <?php if($login_user->is_admin || get_array_value($login_user->permissions, "bu") === "all"){ ?>
                    {name: "created_by", class: "w200", options: <?php echo $team_members_dropdown; ?>}
                <?php } ?>
            ],
            columns: [
                {title: "<?php echo app_lang("id") ?>", "class": "text-center w50"},
                {title: "<?php echo app_lang("bu_name") ?>"},
                {title: "<?php echo app_lang("primary_contact") ?>"},
                {title: "<?php echo app_lang("bu_groups") ?>"},
                {title: "<?php echo app_lang("projects") ?>"},
                {visible: showForecastInfo, searchable: showForecastInfo, title: "<?php echo app_lang("forecast_value") ?>"},
                {visible: showForecastInfo, searchable: showForecastInfo, title: "<?php echo app_lang("actuals_updated") ?>"},
                {visible: showForecastInfo, searchable: showForecastInfo, title: "<?php echo app_lang("due") ?>"}
<?php echo $custom_field_headers; ?>,
                {title: '<i data-feather="menu" class="icon-16"></i>', "class": "text-center option w100", visible: showOptions}
            ],
            printColumns: combineCustomFieldsColumns([0, 1, 2, 3, 4, 5, 6], '<?php echo $custom_field_headers; ?>'),
            xlsColumns: combineCustomFieldsColumns([0, 1, 2, 3, 4, 5, 6], '<?php echo $custom_field_headers; ?>')
        });
    };

    $(document).ready(function () {
        loadClientsTable("#bu-table");

        setTimeout(function () {
            var tab = "<?php echo $tab; ?>";
            if (tab === "contacts") {
                $("[data-bs-target='#contacts']").trigger("click");
            }
        }, 210);
    });
</script>