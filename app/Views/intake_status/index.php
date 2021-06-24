<div id="page-content" class="page-wrapper clearfix">
    <div class="row">
        <div class="col-sm-3 col-lg-2">
            <?php
            $tab_view['active_tab'] = "intake";
            echo view("settings/tabs", $tab_view);
            ?>
        </div>

        <div class="col-sm-9 col-lg-10">
            <div class="card">

                <ul data-bs-toggle="ajax-tab" class="nav nav-tabs bg-white title" role="tablist">
                    <li><a  role="presentation"  href="javascript:;" data-bs-target="#intake-status-tab"> <?php echo app_lang('intake_status'); ?></a></li>
                    <li><a role="presentation" href="<?php echo_uri("intake_source"); ?>" data-bs-target="#intake-source-tab"><?php echo app_lang('intake_source'); ?></a></li>
                    <div class="tab-title clearfix no-border">
                        <div class="title-button-group">
                            <?php echo modal_anchor(get_uri("intake_status/modal_form"), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('add_intake_status'), array("class" => "btn btn-default", "title" => app_lang('add_intake_status'), "id" => "intake-status-source-add-btn")); ?>
                        </div>
                    </div>
                </ul>

                <div class="tab-content">

                    <div role="tabpanel" class="tab-pane fade" id="intake-status-tab">
                        <div class="table-responsive">
                            <table id="intake-status-table" class="display no-thead b-t b-b-only no-hover" cellspacing="0" width="100%">         
                            </table>
                        </div>
                    </div>

                    <div role="tabpanel" class="tab-pane fade" id="intake-source-tab"></div>
                </div>

            </div>
        </div>

    </div>
</div>


<script type="text/javascript">
    $(document).ready(function () {
        $("#intake-status-table").appTable({
            source: '<?php echo_uri("intake_status/list_data") ?>',
            order: [[0, "asc"]],
            hideTools: true,
            displayLength: 100,
            columns: [
                {visible: false},
                {title: '<?php echo app_lang("title"); ?>'},
                {title: '<i data-feather="menu" class="icon-16"></i>', "class": "text-center option w100"}
            ],
            onInitComplete: function () {
                //apply sortable
                $("#intake-status-table").find("tbody").attr("id", "custom-field-table-sortable");
                var $selector = $("#custom-field-table-sortable");

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
                            url: '<?php echo_uri("intake_status/update_field_sort_values") ?>',
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


        //change the add button attributes on changing tab panel
        var addButton = $("#intake-status-source-add-btn");
        $(".nav-tabs li").click(function () {
            var activeField = $(this).find("a").attr("data-bs-target");

            //intake status
            if (activeField === "#intake-status-tab") {
                addButton.attr("title", "<?php echo app_lang("add_intake_status"); ?>");
                addButton.attr("data-title", "<?php echo app_lang("add_intake_status"); ?>");
                addButton.attr("data-action-url", "<?php echo get_uri("intake_status/modal_form"); ?>");

                addButton.html("<?php echo "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('add_intake_status'); ?>");
            } else if (activeField === "#intake-source-tab") {
                addButton.attr("title", "<?php echo app_lang("add_intake_source"); ?>");
                addButton.attr("data-title", "<?php echo app_lang("add_intake_source"); ?>");
                addButton.attr("data-action-url", "<?php echo get_uri("intake_source/modal_form"); ?>");

                addButton.html("<?php echo "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('add_intake_source'); ?>");
            }
        });
    });
</script>