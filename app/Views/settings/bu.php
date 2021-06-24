<div id="page-content" class="page-wrapper clearfix">
    <div class="row">
        <div class="col-sm-3 col-lg-2">
            <?php
            $tab_view['active_tab'] = "bu";
            echo view("settings/tabs", $tab_view);
            ?>
        </div>

        <div class="col-sm-9 col-lg-10">
            <?php echo form_open(get_uri("settings/save_bu_settings"), array("id" => "bu-settings-form", "class" => "general-form dashed-row", "role" => "form")); ?>
            <div class="card">
                <div class=" card-header">
                    <h4><?php echo app_lang("bu_settings"); ?></h4>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <div class="row">
                            <label for="bu_name" class=" col-md-2"><?php echo app_lang('bu_name'); ?></label>
                            <div class=" col-md-10">
                                <?php
                                echo form_input(array(
                                    "id" => "bu_name",
                                    "name" => "bu_name",
                                    "value" => get_setting("bu_name"),
                                    "class" => "form-control",
                                    "placeholder" => app_lang('bu_name'),
                                    "data-rule-required" => true,
                                    "data-msg-required" => app_lang("field_required")
                                ));
                                ?>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="row">
                            <label for="bu_email" class=" col-md-2"><?php echo app_lang('email'); ?></label>
                            <div class=" col-md-10">
                                <?php
                                echo form_input(array(
                                    "id" => "bu_email",
                                    "name" => "bu_email",
                                    "value" => get_setting("bu_email"),
                                    "class" => "form-control",
                                    "placeholder" => app_lang('email')
                                ));
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <label for="bu_website" class=" col-md-2"><?php echo app_lang('website'); ?></label>
                            <div class=" col-md-10">
                                <?php
                                echo form_input(array(
                                    "id" => "bu_website",
                                    "name" => "bu_website",
                                    "value" => get_setting("bu_website"),
                                    "class" => "form-control",
                                    "placeholder" => app_lang('website')
                                ));
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
        $("#bu-settings-form").appForm({
            isModal: false,
            onSuccess: function (result) {
                appAlert.success(result.message, {duration: 10000});
            }
        });

    });
</script>