<li class="js-intake-cookie-tab <?php echo ($active_tab == 'intake_list') ? 'active' : ''; ?>" data-tab="intake_list"><a href="<?php echo_uri('intake'); ?>"><?php echo app_lang("list"); ?></a></li>
<li class="js-intake-cookie-tab <?php echo ($active_tab == 'intake_kanban') ? 'active' : ''; ?>" data-tab="intake_kanban"><a href="<?php echo_uri('intake/all_intake_kanban/'); ?>" ><?php echo app_lang('kanban'); ?></a></li>

<script>
    var selectedTab = getCookie("selected_intake_tab_" + "<?php echo $login_user->id; ?>");

    if (selectedTab && selectedTab !== "<?php echo $active_tab ?>" && selectedTab === "intake_kanban") {
        window.location.href = "<?php echo_uri('intake/all_intake_kanban'); ?>";
    }

    //save the selected tab in browser cookie
    $(document).ready(function () {
        $(".js-intake-cookie-tab").click(function () {
            var tab = $(this).attr("data-tab");
            if (tab) {
                setCookie("selected_intake_tab_" + "<?php echo $login_user->id; ?>", tab);
            }
        });
    });
</script>