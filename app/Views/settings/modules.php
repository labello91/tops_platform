<div id="page-content" class="page-wrapper clearfix">
    <div class="row">
        <div class="col-sm-3 col-lg-2">
            <?php
            $tab_view['active_tab'] = "modules";
            echo view("settings/tabs", $tab_view);
            ?>
        </div>

        <div class="col-sm-9 col-lg-10">
            <?php echo form_open(get_uri("settings/save_module_settings"), array("id" => "module-settings-form", "class" => "general-form dashed-row", "role" => "form")); ?>
            <div class="card">
                <div class="card-header">
                    <h4><?php echo app_lang("manage_modules"); ?></h4>
                    <div><?php echo app_lang("module_settings_instructions"); ?></div>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <div class="row">
                            <label for="module_timeline" class="col-md-2 col-xs-8 col-sm-4"><?php echo app_lang('timeline'); ?></label>
                            <div class="col-md-10 col-xs-4 col-sm-8">
                                <?php
                                echo form_checkbox("module_timeline", "1", get_setting("module_timeline") ? true : false, "id='module_timeline' class='form-check-input ml15'");
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <label for="module_event" class="col-md-2 col-xs-8 col-sm-4"><?php echo app_lang('event'); ?></label>
                            <div class="col-md-10 col-xs-4 col-sm-8">
                                <?php
                                echo form_checkbox("module_event", "1", get_setting("module_event") ? true : false, "id='module_event' class='form-check-input ml15'");
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <label for="module_todo" class="col-md-2 col-xs-8 col-sm-4"><?php echo app_lang('todo'); ?></label>
                            <div class="col-md-10 col-xs-4 col-sm-8">
                                <?php
                                echo form_checkbox("module_todo", "1", get_setting("module_todo") ? true : false, "id='module_todo' class='form-check-input ml15'");
                                ?>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="row">
                            <label for="module_note" class="col-md-2 col-xs-8 col-sm-4"><?php echo app_lang('note'); ?></label>
                            <div class="col-md-10 col-xs-4 col-sm-8">
                                <?php
                                echo form_checkbox("module_note", "1", get_setting("module_note") ? true : false, "id='module_note' class='form-check-input ml15'");
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <label for="module_message" class="col-md-2 col-xs-8 col-sm-4"><?php echo app_lang('message'); ?></label>
                            <div class="col-md-10 col-xs-4 col-sm-8">
                                <?php
                                echo form_checkbox("module_message", "1", get_setting("module_message") ? true : false, "id='module_message' class='form-check-input ml15'");
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <label for="module_chat" class="col-md-2 col-xs-8 col-sm-4"><?php echo app_lang('chat'); ?></label>
                            <div class="col-md-10 col-xs-4 col-sm-8">
                                <?php
                                echo form_checkbox("module_chat", "1", get_setting("module_chat") ? true : false, "id='module_chat' class='form-check-input ml15'");
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <label for="module_forecast" class="col-md-2 col-xs-8 col-sm-4"><?php echo app_lang('forecast'); ?></label>
                            <div class="col-md-10 col-xs-4 col-sm-8">
                                <?php
                                echo form_checkbox("module_forecast", "1", get_setting("module_forecast") ? true : false, "id='module_forecast' class='form-check-input ml15'");
                                ?>
                            </div>
                        </div>
                    </div>    
                    <div class="form-group">
                        <div class="row">
                            <label for="module_actuals" class="col-md-2 col-xs-8 col-sm-4"><?php echo app_lang('actuals'); ?></label>
                            <div class="col-md-10 col-xs-4 col-sm-8">
                                <?php
                                echo form_checkbox("module_actuals", "1", get_setting("module_actuals") ? true : false, "id='module_actuals' class='form-check-input ml15'");
                                ?>
                            </div>
                        </div>
                    </div>  
                    <div class="form-group">
                        <div class="row">
                            <label for="module_order" class="col-md-2 col-xs-8 col-sm-4"><?php echo app_lang('order'); ?></label>
                            <div class="col-md-10 col-xs-4 col-sm-8">
                                <?php
                                echo form_checkbox("module_order", "1", get_setting("module_order") ? true : false, "id='module_order' class='form-check-input ml15'");
                                ?>
                            </div>
                        </div>
                    </div>  

                    <div class="form-group">
                        <div class="row">
                            <label for="module_intake" class="col-md-2 col-xs-8 col-sm-4"><?php echo app_lang('intake'); ?></label>
                            <div class="col-md-10 col-xs-4 col-sm-8">
                                <?php
                                echo form_checkbox("module_intake", "1", get_setting("module_intake") ? true : false, "id='module_intake' class='form-check-input ml15'");
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <label for="module_ticket" class="col-md-2 col-xs-8 col-sm-4"><?php echo app_lang('ticket'); ?></label>
                            <div class="col-md-10 col-xs-4 col-sm-8">
                                <?php
                                echo form_checkbox("module_ticket", "1", get_setting("module_ticket") ? true : false, "id='module_ticket' class='form-check-input ml15'");
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <label for="module_announcement" class="col-md-2 col-xs-8 col-sm-4"><?php echo app_lang('announcement'); ?></label>
                            <div class="col-md-10 col-xs-4 col-sm-8">
                                <?php
                                echo form_checkbox("module_announcement", "1", get_setting("module_announcement") ? true : false, "id='module_announcement' class='form-check-input ml15'");
                                ?>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="row">
                            <label for="module_gantt" class="col-md-2"><?php echo app_lang('gantt'); ?></label>
                            <div class="col-md-10">
                                <?php
                                echo form_checkbox("module_gantt", "1", get_setting("module_gantt") ? true : false, "id='module_gantt' class='form-check-input ml15'");
                                ?>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="row">
                            <label for="module_help" class="col-md-2 col-xs-8 col-sm-4"><?php echo app_lang('help') . " (" . app_lang("team_members") . ")"; ?></label>
                            <div class="col-md-10 col-xs-4 col-sm-8">
                                <?php
                                echo form_checkbox("module_help", "1", get_setting("module_help") ? true : false, "id='module_help' class='form-check-input ml15'");
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <label for="module_knowledge_base" class="col-md-2 col-xs-8 col-sm-4"><?php echo app_lang('knowledge_base') . " (" . app_lang("public") . ")"; ?></label>
                            <div class="col-md-10 col-xs-4 col-sm-8">
                                <?php
                                echo form_checkbox("module_knowledge_base", "1", get_setting("module_knowledge_base") ? true : false, "id='module_knowledge_base' class='form-check-input ml15'");
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary"><span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang('save'); ?></button>
                </div>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $("#module-settings-form").appForm({
            isModal: false,
            onSuccess: function (result) {
                appAlert.success(result.message, {duration: 10000});
                location.reload();
            }
        });
    });
</script>