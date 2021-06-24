<?php echo view("includes/cropbox"); ?>
<div id="page-content" class="clearfix">
    <div class="bg-dark-success clearfix">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6">
                    <div class="p20 row">
                        <?php echo view("users/profile_image_section"); ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="p20 row">
                        <p> 
                            <?php
                            $bu_link = anchor(get_uri("bu/view/" . $bu_info->id), $bu_info->bu_name, array("class" => "white-link"));

                            if ($login_user->user_type === "bu") {
                                $bu_link = anchor(get_uri("bu/contact_profile/" . $login_user->id . "/bu"), $bu_info->bu_name, array("class" => "white-link"));
                            }

                            echo app_lang("bu_name") . ": <b>" . $bu_link . "</b>";
                            ?>

                        </p>
                        <?php if ($bu_info->first_name) { ?>
                            <p><?php echo nl2br($bu_info->first_name); ?>
                                <?php if ($bu_info->last_name) { ?>
                                    <br /><?php echo $bu_info->last_name; ?>
                                <?php } ?>
                                <?php if ($bu_info->job_title) { ?>
                                    <br /><?php echo $bu_info->job_title; ?>
                                <?php } ?>
                                <?php if ($bu_info->slack_username) { ?>
                                    <br /><?php echo $bu_info->slack_username; ?>
                                <?php } ?>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <ul id="bu-contact-tabs" data-bs-toggle="ajax-tab" class="nav nav-tabs scrollable-tabs b-b rounded-0" role="tablist">
        <li><a  role="presentation" href="<?php echo_uri("bu/contact_general_info_tab/" . $user_info->id); ?>" data-bs-target="#tab-general-info"> <?php echo app_lang('general_info'); ?></a></li>
        <li><a  role="presentation" href="<?php echo_uri("bu/bu_info_tab/" . $user_info->bu_id); ?>" data-bs-target="#tab-bu-info"> <?php echo app_lang('bu'); ?></a></li>
        <li><a role="presentation" href="<?php echo_uri("bu/account_settings/" . $user_info->id); ?>" data-bs-target="#tab-account-settings"> <?php echo app_lang('account_settings'); ?></a></li>
        <?php if ($user_info->id == $login_user->id) { ?>
            <li><a role="presentation" href="<?php echo_uri("bu/my_preferences/" . $user_info->id); ?>" data-bs-target="#tab-my-preferences"> <?php echo app_lang('my_preferences'); ?></a></li>
        <?php } ?>
        <?php if ($user_info->id == $login_user->id && !get_setting("disable_editing_left_menu_by_bu")) { ?>
            <li><a role="presentation" href="<?php echo_uri("left_menus/index/user"); ?>" data-bs-target="#tab-user-left-menu"> <?php echo app_lang('left_menu'); ?></a></li>
        <?php } ?>
        <?php if ($user_info->id == $login_user->id && get_setting("enable_gdpr") && (get_setting("bu_can_request_account_removal") || get_setting("allow_bu_to_export_their_data"))) { ?>
            <li><a role="presentation" href="<?php echo_uri("bu/gdpr/" . $user_info->id); ?>" data-bs-target="#tab-gdpr">GDPR</a></li>
        <?php } ?>
    </ul>

    <div class="tab-content">
        <div role="tabpanel" class="tab-pane fade" id="tab-files"></div>
        <div role="tabpanel" class="tab-pane fade" id="tab-general-info"></div>
        <div role="tabpanel" class="tab-pane fade" id="tab-bu-info"></div>
        <div role="tabpanel" class="tab-pane fade" id="tab-social-links"></div>
        <div role="tabpanel" class="tab-pane fade" id="tab-account-settings"></div>
        <div role="tabpanel" class="tab-pane fade" id="tab-my-preferences"></div>
        <div role="tabpanel" class="tab-pane fade" id="tab-user-left-menu"></div>
        <div role="tabpanel" class="tab-pane fade" id="tab-gdpr"></div>

    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        $(".upload").change(function () {
            if (typeof FileReader == 'function' && !$(this).hasClass("hidden-input-file")) {
                showCropBox(this);
            } else {
                $("#profile-image-form").submit();
            }
        });
        $("#profile_image").change(function () {
            $("#profile-image-form").submit();
        });


        $("#profile-image-form").appForm({
            isModal: false,
            beforeAjaxSubmit: function (data) {
                $.each(data, function (index, obj) {
                    if (obj.name === "profile_image") {
                        var profile_image = replaceAll(":", "~", data[index]["value"]);
                        data[index]["value"] = profile_image;
                    }
                });
            },
            onSuccess: function (result) {
                if (typeof FileReader == 'function' && !result.reload_page) {
                    appAlert.success(result.message, {duration: 10000});
                } else {
                    location.reload();
                }
            }
        });

        setTimeout(function () {
            var tab = "<?php echo $tab; ?>";
            if (tab === "general") {
                $("[data-bs-target='#tab-general-info']").trigger("click");
            } else if (tab === "bu") {
                $("[data-bs-target='#tab-bu-info']").trigger("click");
            } else if (tab === "account") {
                $("[data-bs-target='#tab-account-settings']").trigger("click");
            } else if (tab === "my_preferences") {
                $("[data-bs-target='#tab-my-preferences']").trigger("click");
            } else if (tab === "left_menu") {
                $("[data-bs-target='#tab-user-left-menu']").trigger("click");
            }
        }, 210);

    });
</script>