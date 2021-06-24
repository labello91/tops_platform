<div id="page-content" class="page-wrapper clearfix">
    <div class="row">
        <div class="col-sm-3 col-lg-2">
            <?php
            $tab_view['active_tab'] = "custom_fields";
            echo view("settings/tabs", $tab_view);
            ?>
        </div>
        <div class="col-sm-9 col-lg-10">

            <div class="card no-border clearfix">

                <ul id="custom-field-tab" data-bs-toggle="ajax-tab" class="nav nav-tabs bg-white title scrollable-tabs" role="tablist">
                    <li class="title-tab"><h4 class="pl15 pt10 pr15"><?php echo app_lang("custom_fields"); ?></h4></li>
                    <li><a role="presentation" data-related_to="bu"  href="javascript:;" data-bs-target="#custom-field-bu"><?php echo app_lang("bu"); ?></a></li>
                    <li><a role="presentation" data-related_to="bu_contacts" class="" href="<?php echo_uri("custom_fields/bu_contacts/"); ?>" data-bs-target="#custom-field-bu_contacts"><?php echo app_lang("bu_contacts"); ?></a></li>
                    <li><a role="presentation" data-related_to="intake"  href="<?php echo_uri("custom_fields/intake/"); ?>" data-bs-target="#custom-field-intake"><?php echo app_lang("intake"); ?></a></li>
                    <li><a role="presentation" data-related_to="intake_contacts" class="" href="<?php echo_uri("custom_fields/intake_contacts/"); ?>" data-bs-target="#custom-field-intake_contacts"><?php echo app_lang("intake_contacts"); ?></a></li>
                    <li><a role="presentation" data-related_to="projects" href="<?php echo_uri("custom_fields/projects/"); ?>" data-bs-target="#custom-field-projects"><?php echo app_lang('projects'); ?></a></li>
                    <li><a role="presentation" data-related_to="tasks" href="<?php echo_uri("custom_fields/tasks/"); ?>" data-bs-target="#custom-field-tasks"><?php echo app_lang('tasks'); ?></a></li>
                    <li><a role="presentation" data-related_to="team_members" href="<?php echo_uri("custom_fields/team_members/"); ?>" data-bs-target="#custom-field-team_members"><?php echo app_lang('team_members'); ?></a></li>
                    <li><a role="presentation" data-related_to="tickets" href="<?php echo_uri("custom_fields/tickets/"); ?>" data-bs-target="#custom-field-tickets"><?php echo app_lang('tickets'); ?></a></li>
                    <li><a role="presentation" data-related_to="forecast" href="<?php echo_uri("custom_fields/forecast/"); ?>" data-bs-target="#custom-field-forecast"><?php echo app_lang('forecast'); ?></a></li>
                    <li><a role="presentation" data-related_to="events" href="<?php echo_uri("custom_fields/events/"); ?>" data-bs-target="#custom-field-events"><?php echo app_lang('events'); ?></a></li>
                    <li><a role="presentation" data-related_to="actuals" href="<?php echo_uri("custom_fields/actuals/"); ?>" data-bs-target="#custom-field-actuals"><?php echo app_lang('actuals'); ?></a></li>
                    <div class="tab-title clearfix no-border">
                        <div class="title-button-group">
                            <?php echo modal_anchor(get_uri("custom_fields/modal_form/"), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('add_field'), array("class" => "btn btn-default", "id" => "add-field-button", "data-post-related_to" => "bu", "title" => app_lang('add_field'))); ?>
                        </div>
                    </div>
                </ul>


                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane fade active clearfix" id="custom-field-bu">
                        <div class="card mb0 p20">
                            <div class="table-responsive general-form">
                                <table id="custom-field-table-bu" class="display no-thead b-t b-b-only no-hover" cellspacing="0" width="100%">            
                                </table>
                            </div>
                        </div>
                    </div>
                    <div role="tabpanel" class="tab-pane fade" id="custom-field-bu_contacts"></div>
                    <div role="tabpanel" class="tab-pane fade" id="custom-field-intake_contacts"></div>
                    <div role="tabpanel" class="tab-pane fade" id="custom-field-intake"></div>
                    <div role="tabpanel" class="tab-pane fade" id="custom-field-projects"></div>
                    <div role="tabpanel" class="tab-pane fade" id="custom-field-tasks"></div>
                    <div role="tabpanel" class="tab-pane fade" id="custom-field-team_members"></div>
                    <div role="tabpanel" class="tab-pane fade" id="custom-field-tickets"></div>
                    <div role="tabpanel" class="tab-pane fade" id="custom-field-forecast"></div>
                    <div role="tabpanel" class="tab-pane fade" id="custom-field-events"></div>
                    <div role="tabpanel" class="tab-pane fade" id="custom-field-actuals"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $("#custom-field-tab a").click(function () {
            $("#add-field-button").attr("data-post-related_to", $(this).attr("data-related_to"));
        });

        setTimeout(function () {
            var tab = "<?php echo $tab; ?>";
            if (tab) {
                $("[data-bs-target='#custom-field-" + tab + "']").trigger("click");
            }
        }, 210);


        loadCustomFieldTable("bu");

    });

    loadCustomFieldTable = function (relatedTo) {

        $("#custom-field-table-" + relatedTo).appTable({
            source: '<?php echo_uri("custom_fields/list_data") ?>' + "/" + relatedTo,
            order: [[1, "asc"]],
            hideTools: true,
            displayLength: 100,
            columns: [
                {title: '<?php echo app_lang("title") ?>'},
                {visible: false},
                {title: '<i data-feather="menu" class="icon-16"></i>', "class": "text-right option w100"}
            ],
            onInitComplete: function () {
                //apply sortable
                $("#custom-field-table-" + relatedTo).find("tbody").attr("id", "custom-field-table-sortable-" + relatedTo);
                var $selector = $("#custom-field-table-sortable-" + relatedTo);

                Sortable.create($selector[0], {
                    animation: 150,
                    chosenClass: "sortable-chosen",
                    ghostClass: "sortable-ghost",
                    onUpdate: function (e) {
                        appLoader.show();
                        //prepare sort indexes 
                        var data = "";
                        $.each($selector.find(".field-row"), function (index, ele) {
                            if (data) {
                                data += ",";
                            }

                            data += $(ele).attr("data-id") + "-" + index;
                        });

                        //update sort indexes
                        $.ajax({
                            url: '<?php echo_uri("custom_fields/update_field_sort_values") ?>' + "/" + relatedTo,
                            type: "POST",
                            data: {sort_values: data},
                            success: function () {
                                appLoader.hide();
                            }
                        });
                    }
                });

            }
        });
    };


</script>