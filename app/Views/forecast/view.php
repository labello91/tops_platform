<div id="page-content" class="clearfix">
    <div style="max-width: 1000px; margin: auto;">
        <div class="page-title clearfix mt25">
            <h1><?php echo get_forecast_id($forecast_info->id); ?>
                <?php
                if ($forecast_info->recurring) {
                    $recurring_status_class = "text-primary";
                    if ($forecast_info->no_of_cycles_completed > 0 && $forecast_info->no_of_cycles_completed == $forecast_info->no_of_cycles) {
                        $recurring_status_class = "text-danger";
                    }
                    ?>
                    <span class="label ml15 b-a "><span class="<?php echo $recurring_status_class; ?>"><?php echo app_lang('recurring'); ?></span></span>
                <?php } ?>
            </h1>
            <div class="title-button-group">
                <span class="dropdown inline-block mt10">
                    <button class="btn btn-info text-white dropdown-toggle caret mt0 mb0" type="button" data-bs-toggle="dropdown" aria-expanded="true">
                        <i data-feather="tool" class="icon-16"></i> <?php echo app_lang('actions'); ?>
                    </button>
                    <ul class="dropdown-menu" role="menu">
                        <?php if ($forecast_status !== "cancelled" && $can_edit_forecast) { ?>
                            <li role="presentation"><?php echo modal_anchor(get_uri("forecast/send_forecast_modal_form/" . $forecast_info->id), "<i data-feather='mail' class='icon-16'></i> " . app_lang('email_forecast_to_bu'), array("title" => app_lang('email_forecast_to_bu'), "data-post-id" => $forecast_info->id, "role" => "menuitem", "tabindex" => "-1", "class" => "dropdown-item")); ?> </li>
                        <?php } ?>
                        <li role="presentation"><?php echo anchor(get_uri("forecast/download_pdf/" . $forecast_info->id), "<i data-feather='download' class='icon-16'></i> " . app_lang('download_pdf'), array("title" => app_lang('download_pdf'), "class" => "dropdown-item")); ?> </li>
                        <li role="presentation"><?php echo anchor(get_uri("forecast/download_pdf/" . $forecast_info->id . "/view"), "<i data-feather='file-text' class='icon-16'></i> " . app_lang('view_pdf'), array("title" => app_lang('view_pdf'), "target" => "_blank", "class" => "dropdown-item")); ?> </li>
                        <li role="presentation"><?php echo anchor(get_uri("forecast/preview/" . $forecast_info->id . "/1"), "<i data-feather='search' class='icon-16'></i> " . app_lang('forecast_preview'), array("title" => app_lang('forecast_preview'), "target" => "_blank", "class" => "dropdown-item")); ?> </li>
                        <li role="presentation"><?php echo js_anchor("<i data-feather='printer' class='icon-16'></i> " . app_lang('print_forecast'), array('title' => app_lang('print_forecast'), 'id' => 'print-forecast-btn', "class" => "dropdown-item")); ?> </li>

                        <?php if ($can_edit_forecast) { ?>
                            <li role="presentation" class="dropdown-divider"></li>

                            <?php if ($forecast_status !== "cancelled") { ?>
                                <li role="presentation"><?php echo modal_anchor(get_uri("forecast/modal_form"), "<i data-feather='edit' class='icon-16'></i> " . app_lang('edit_forecast'), array("title" => app_lang('edit_forecast'), "data-post-id" => $forecast_info->id, "role" => "menuitem", "tabindex" => "-1", "class" => "dropdown-item")); ?> </li>
                            <?php } ?>

                            <?php if ($forecast_status == "draft" && $forecast_status !== "cancelled") { ?>
                                <li role="presentation"><?php echo ajax_anchor(get_uri("forecast/update_forecast_status/" . $forecast_info->id . "/not_achieved"), "<i data-feather='check' class='icon-16'></i> " . app_lang('mark_forecast_as_not_achieved'), array("data-reload-on-success" => "1", "class" => "dropdown-item")); ?> </li>
                            <?php } else if ($forecast_status == "not_achieved" || $forecast_status == "overdue" || $forecast_status == "partially_achieved") { ?>
                                <li role="presentation"><?php echo js_anchor("<i data-feather='x' class='icon-16'></i> " . app_lang('mark_forecast_as_cancelled'), array('title' => app_lang('mark_forecast_as_cancelled'), "data-action-url" => get_uri("forecast/update_forecast_status/" . $forecast_info->id . "/cancelled"), "data-action" => "delete-confirmation", "data-reload-on-success" => "1", "class" => "dropdown-item")); ?> </li>
                            <?php } ?>
                            <li role="presentation"><?php echo modal_anchor(get_uri("forecast/modal_form"), "<i data-feather='copy' class='icon-16'></i> " . app_lang('clone_forecast'), array("data-post-is_clone" => true, "data-post-id" => $forecast_info->id, "title" => app_lang('clone_forecast'), "class" => "dropdown-item")); ?></li>
                        <?php } ?>

                    </ul>
                </span>
                <?php if ($forecast_status !== "cancelled" && $can_edit_forecast) { ?>
                    <?php echo modal_anchor(get_uri("forecast/item_modal_form"), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('add_item'), array("class" => "btn btn-default", "title" => app_lang('add_item'), "data-post-forecast_id" => $forecast_info->id)); ?>
                    <?php echo modal_anchor(get_uri("forecast_actuals/actuals_modal_form"), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('add_actuals'), array("class" => "btn btn-default", "title" => app_lang('add_actuals'), "data-post-forecast_id" => $forecast_info->id)); ?>
                <?php } ?>
            </div>
        </div>

        <div id="forecast-status-bar">
            <?php echo view("forecast/forecast_status_bar"); ?>
        </div>

        <?php
        if ($forecast_info->recurring) {
            echo view("forecast/forecast_recurring_info_bar");
        }
        ?>

        <div class="mt15">
            <div class="card p15 b-t">
                <div class="clearfix p20">
                    <!-- small font size is required to generate the pdf, overwrite that for screen -->
                    <style type="text/css"> .forecast-meta {font-size: 100% !important;}</style>

                    <?php
                    $color = get_setting("forecast_color");
                    if (!$color) {
                        $color = "#2AA384";
                    }
                    $forecast_style = get_setting("forecast_style");
                    $data = array(
                        "bu_info" => $bu_info,
                        "color" => $color,
                        "forecast_info" => $forecast_info
                    );

                    if ($forecast_style === "style_2") {
                        echo view('forecast/forecast_parts/header_style_2.php', $data);
                    } else {
                        echo view('forecast/forecast_parts/header_style_1.php', $data);
                    }
                    ?>
                </div>

                <div class="table-responsive mt15 pl15 pr15">
                    <table id="forecast-item-table" class="display" width="100%">            
                    </table>
                </div>

                <div class="clearfix">
                    <div class="float-end pr15" id="forecast-total-section">
                        <?php echo view("forecast/forecast_total_section", array("forecast_id" => $forecast_info->id, "can_edit_forecast" => $can_edit_forecast)); ?>
                    </div>
                </div>

                <?php
                $files = @unserialize($forecast_info->files);
                if ($files && is_array($files) && count($files)) {
                    ?>
                    <div class="clearfix">
                        <div class="col-md-12 mt20">
                            <p class="b-t"></p>
                            <div class="mb5 strong"><?php echo app_lang("files"); ?></div>
                            <?php
                            foreach ($files as $key => $value) {
                                $file_name = get_array_value($value, "file_name");
                                echo "<div>";
                                echo js_anchor(remove_file_prefix($file_name), array("data-toggle" => "app-modal", "data-sidebar" => "0", "data-url" => get_uri("forecast/file_preview/" . $forecast_info->id . "/" . $key)));
                                echo "</div>";
                            }
                            ?>
                        </div>
                    </div>
                <?php } ?>

                <p class="b-t b-info pt10 m15"><?php echo nl2br($forecast_info->note); ?></p>

            </div>
        </div>



        <?php if ($forecast_info->recurring) { ?>
            <ul id="forecast-view-tabs" data-bs-toggle="ajax-tab" class="nav nav-tabs" role="tablist">
                <li><a  role="presentation" href="#" data-bs-target="#forecast-actuals"> <?php echo app_lang('actuals'); ?></a></li>
                <li><a  role="presentation" href="<?php echo_uri("forecast/sub_forecast/" . $forecast_info->id); ?>" data-bs-target="#sub-forecast"> <?php echo app_lang('sub_forecast'); ?></a></li>
            </ul>

            <div class="tab-content">
                <div role="tabpanel" class="tab-pane fade active" id="forecast-actuals">
                    <div class="card">
                        <div class="tab-title clearfix">
                            <h4> <?php echo app_lang('forecast_actuals_list'); ?></h4>
                        </div>
                        <div class="table-responsive">
                            <table id="forecast-actuals-table" class="display" cellspacing="0" width="100%">            
                            </table>
                        </div>
                    </div>
                </div>
                <div role="tabpanel" class="tab-pane fade" id="sub-forecast"></div>
            </div>
        <?php } else { ?>

            <div class="card">
                <div class="tab-title clearfix">
                    <h4> <?php echo app_lang('forecast_actuals_list'); ?></h4>
                </div>
                <div class="table-responsive">
                    <table id="forecast-actuals-table" class="display" cellspacing="0" width="100%">            
                    </table>
                </div>
            </div>
        <?php } ?>
    </div>
</div>



<script type="text/javascript">
    $(document).ready(function () {
        var optionVisibility = false;
        if ("<?php echo $can_edit_forecast ?>") {
            optionVisibility = true;
        }

        $("#forecast-item-table").appTable({
            source: '<?php echo_uri("forecast/item_list_data/" . $forecast_info->id . "/") ?>',
            order: [[0, "asc"]],
            hideTools: true,
            displayLength: 100,
            columns: [
                {visible: false, searchable: false},
                {title: '<?php echo app_lang("item") ?> ', "bSortable": false},
                {title: '<?php echo app_lang("quantity") ?>', "class": "text-right w15p", "bSortable": false},
                {title: '<?php echo app_lang("cost") ?>', "class": "text-right w15p", "bSortable": false},
                {title: '<?php echo app_lang("total") ?>', "class": "text-right w15p", "bSortable": false},
                {title: '<i data-feather="menu" class="icon-16"></i>', "class": "text-center option w100", "bSortable": false, visible: optionVisibility}
            ],
            onInitComplete: function () {
<?php if ($can_edit_forecast) { ?>
                    //apply sortable
                    $("#forecast-item-table").find("tbody").attr("id", "forecast-item-table-sortable");
                    var $selector = $("#forecast-item-table-sortable");

                    Sortable.create($selector[0], {
                        animation: 150,
                        chosenClass: "sortable-chosen",
                        ghostClass: "sortable-ghost",
                        onUpdate: function (e) {
                            appLoader.show();
                            //prepare sort indexes 
                            var data = "";
                            $.each($selector.find(".item-row"), function (index, ele) {
                                if (data) {
                                    data += ",";
                                }

                                data += $(ele).attr("data-id") + "-" + index;
                            });

                            //update sort indexes
                            $.ajax({
                                url: '<?php echo_uri("Forecast/update_item_sort_values") ?>',
                                type: "POST",
                                data: {sort_values: data},
                                success: function () {
                                    appLoader.hide();
                                }
                            });
                        }
                    });

<?php } ?>

            },
            onDeleteSuccess: function (result) {
                $("#forecast-total-section").html(result.forecast_total_view);
                if (typeof updateForecastStatusBar == 'function') {
                    updateForecastStatusBar(result.forecast_id);
                }
            },
            onUndoSuccess: function (result) {
                $("#forecast-total-section").html(result.forecast_total_view);
                if (typeof updateForecastStatusBar == 'function') {
                    updateForecastStatusBar(result.forecast_id);
                }
            }
        });

        $("#forecast-actuals-table").appTable({
            source: '<?php echo_uri("forecast_actuals/actuals_list_data/" . $forecast_info->id . "/") ?>',
            order: [[0, "asc"]],
            columns: [
                {targets: [0], visible: false, searchable: false},
                {visible: false, searchable: false},
                {title: '<?php echo app_lang("actuals_date") ?> ', "class": "w15p", "iDataSort": 1},
                {title: '<?php echo app_lang("actuals_method") ?>', "class": "w15p"},
                {title: '<?php echo app_lang("note") ?>'},
                {title: '<?php echo app_lang("amount") ?>', "class": "text-right w15p"},
                {title: '<i data-feather="menu" class="icon-16"></i>', "class": "text-center option w100", visible: optionVisibility}
            ],
            onDeleteSuccess: function (result) {
                $("#forecast-total-section").html(result.forecast_total_view);
                if (typeof updateForecastStatusBar == 'function') {
                    updateForecastStatusBar(result.forecast_id);
                }
            },
            onUndoSuccess: function (result) {
                $("#forecast-total-section").html(result.forecast_total_view);
                if (typeof updateForecastStatusBar == 'function') {
                    updateForecastStatusBar(result.forecast_id);
                }
            }
        });

        //modify the delete confirmation texts
        $("#confirmationModalTitle").html("<?php echo app_lang('cancel') . "?"; ?>");
        $("#confirmDeleteButton").html("<i data-feather='x' class='icon-16'></i> <?php echo app_lang("cancel"); ?>");
    });

    updateForecastStatusBar = function (forecastId) {
        $.ajax({
            url: "<?php echo get_uri("forecast/get_forecast_status_bar"); ?>/" + forecastId,
            success: function (result) {
                if (result) {
                    $("#forecast-status-bar").html(result);
                }
            }
        });
    };

    //print forecast
    $("#print-forecast-btn").click(function () {
        appLoader.show();

        $.ajax({
            url: "<?php echo get_uri('forecast/print_forecast/' . $forecast_info->id) ?>",
            dataType: 'json',
            success: function (result) {
                if (result.success) {
                    document.body.innerHTML = result.print_view; //add forecast's print view to the page
                    $("html").css({"overflow": "visible"});

                    setTimeout(function () {
                        window.print();
                    }, 200);
                } else {
                    appAlert.error(result.message);
                }

                appLoader.hide();
            }
        });
    });

    //reload page after finishing print action
    window.onafterprint = function () {
        location.reload();
    };

</script>

<?php
//required to send email 

load_css(array(
    "assets/js/summernote/summernote.css",
));
load_js(array(
    "assets/js/summernote/summernote.min.js",
));
?>

