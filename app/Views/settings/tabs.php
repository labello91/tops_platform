<?php
$settings_menu = array(
    "app_settings" => array(
        array("name" => "general", "url" => "settings/general"),
        array("name" => "email", "url" => "settings/email"),
        array("name" => "email_templates", "url" => "email_templates"),
        array("name" => "modules", "url" => "settings/modules"),
        array("name" => "cron_job", "url" => "settings/cron_job"),
        array("name" => "notifications", "url" => "settings/notifications"),
        array("name" => "integration", "url" => "settings/integration"),
        array("name" => "updates", "url" => "Updates"),
    ),
    "access_permission" => array(
        array("name" => "roles", "url" => "roles"),
        array("name" => "team", "url" => "team"),
    ),
    "bu" => array(
        array("name" => "bu_permissions", "url" => "settings/bu_permissions"),
        array("name" => "bu_groups", "url" => "bu_groups"),
        array("name" => "dashboard", "url" => "dashboard/bu_default_dashboard"),
        array("name" => "bu_left_menu", "url" => "left_menus/index/bu_default"),
        array("name" => "bu_projects", "url" => "settings/bu_projects"),
    ),
    "setup" => array(
        array("name" => "custom_fields", "url" => "custom_fields"),
        array("name" => "tasks", "url" => "task_status"),
    )
);

//restricted settings

if (get_setting("module_event") == "1") {
    $settings_menu["setup"][] = array("name" => "events", "url" => "settings/events");
}


if (get_setting("module_ticket") == "1") {
    $settings_menu["setup"][] = array("name" => "tickets", "url" => "ticket_types");
}

if (get_setting("module_actuals") == "1") {
    $settings_menu["setup"][] = array("name" => "actuals_categories", "url" => "actuals_categories");
}

if (get_setting("module_forecast") == "1" || get_setting("module_estimate") == "1") {
    $settings_menu["setup"][] = array("name" => "item_categories", "url" => "item_categories");
}

if (get_setting("module_forecast") == "1") {
    $settings_menu["setup"][] = array("name" => "forecast", "url" => "settings/forecast");
}


$settings_menu["setup"][] = array("name" => "actuals_methods", "url" => "actuals_methods");
$settings_menu["setup"][] = array("name" => "bu", "url" => "settings/bu");


if (get_setting("module_intake") == "1") {
    $settings_menu["setup"][] = array("name" => "intake", "url" => "intake_status");
}

$settings_menu["setup"][] = array("name" => "projects", "url" => "settings/projects");


$settings_menu["setup"][] = array("name" => "gdpr", "url" => "settings/gdpr");
$settings_menu["setup"][] = array("name" => "pages", "url" => "pages");

$settings_menu["setup"][] = array("name" => "left_menu", "url" => "left_menus");

$settings_menu["setup"][] = array("name" => "footer", "url" => "settings/footer");
?>

<ul class="nav nav-tabs vertical settings d-block" role="tablist">
    <?php
    foreach ($settings_menu as $key => $value) {

        //collapse the selected settings tab panel
        $collapse_in = "";
        $collapsed_class = "collapsed";
        if (in_array($active_tab, array_column($value, "name"))) {
            $collapse_in = "show";
            $collapsed_class = "";
        }
        ?>

        <div class="clearfix settings-anchor <?php echo $collapsed_class; ?>" data-bs-toggle="collapse" data-bs-target="#settings-tab-<?php echo $key; ?>">
            <?php echo app_lang($key); ?>
        </div>

        <?php
        echo "<div id='settings-tab-$key' class='collapse $collapse_in'>";
        echo "<ul class='list-group help-catagory'>";

        foreach ($value as $sub_setting) {
            $active_class = "";
            $setting_name = get_array_value($sub_setting, "name");
            $setting_url = get_array_value($sub_setting, "url");

            if ($active_tab == $setting_name) {
                $active_class = "active";
            }

            echo "<a href='" . get_uri($setting_url) . "' class='list-group-item $active_class'>" . app_lang($setting_name) . "</a>";
        }

        echo "</ul>";
        echo "</div>";
    }
    ?>
</ul>