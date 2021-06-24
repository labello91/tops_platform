<?php echo view("includes/cropbox"); ?>
<div id="page-content" class="clearfix">
    <div class="bg-success p20">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6">
                    <?php echo view("users/profile_image_section"); ?>
                </div>
                <div class="col-md-6">
                    <p> 
                        <?php
                        $bu_link = anchor(get_uri("intake/view/" . $intake_info->id), $intake_info->bu_name, array("class" => "white-link"));

                        if ($login_user->user_type === "bu") {
                            $bu_link = anchor(get_uri("intake/contact_profile/" . $login_user->id . "/bu"), $intake_info->bu_name, array("class" => "white-link"));
                        }

                        echo app_lang("bu_name") . ": <b>" . $bu_link . "</b>";
                        ?>

                    </p>
                    <?php if ($intake_info->job_title) { ?>
                        <p><?php echo nl2br($intake_info->job_title); ?>
                            <?php if ($intake_info->country) { ?>
                                <br /><?php echo $intake_info->country; ?>
                            <?php } ?>
                            <?php if ($intake_info->state) { ?>
                                <br /><?php echo $intake_info->state; ?>
                            <?php } ?>
                        </p>
                        <p>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>


    <ul data-bs-toggle="ajax-tab" class="nav nav-tabs no-border-top-radius" role="tablist">
        <li><a  role="presentation" href="<?php echo_uri("intake/contact_general_info_tab/" . $user_info->id); ?>" data-bs-target="#tab-general-info"> <?php echo app_lang('general_info'); ?></a></li>
        <li><a  role="presentation" href="<?php echo_uri("intake/bu_info_tab/" . $user_info->bu_id); ?>" data-bs-target="#tab-bu-info"> <?php echo app_lang('bu'); ?></a></li>
    </ul>

    <div class="tab-content">
        <div role="tabpanel" class="tab-pane fade" id="tab-general-info"></div>
        <div role="tabpanel" class="tab-pane fade" id="tab-bu-info"></div>

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

        var tab = "<?php echo $tab; ?>";
        if (tab === "general") {
            $("[data-bs-target='#tab-general-info']").trigger("click");
        } else if (tab === "bu") {
            $("[data-bs-target='#tab-bu-info']").trigger("click");
        } else if (tab === "my_preferences") {
            $("[data-bs-target='#tab-my-preferences']").trigger("click");
        }

    });
</script>