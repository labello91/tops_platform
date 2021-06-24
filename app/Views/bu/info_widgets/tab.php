<?php
$card = "";
$icon = "";
$value = "";
$link = "";

$view_type = "bu_dashboard";
if ($login_user->user_type == "team_member") {
    $view_type = "";
}

if (!is_object($bu_info)) {
    $bu_info = new stdClass();
}


if ($tab == "projects") {
    $card = "bg-info";
    $icon = "grid";
    if (property_exists($bu_info, "total_projects")) {
        $value = to_decimal_format($bu_info->total_projects);
    }
    if ($view_type == "bu_dashboard") {
        $link = get_uri('projects/index');
    } else {
        $link = get_uri('bu/view/' . $bu_info->id . '/projects');
    }
} else if ($tab == "forecast_value") {
    $card = "bg-primary";
    $icon = "file-text";
    if (property_exists($bu_info, "forecast_value")) {
        $value = to_currency($bu_info->forecast_value, $bu_info->currency_symbol);
    }
    if ($view_type == "bu_dashboard") {
        $link = get_uri('forecast/index');
    } else {
        $link = get_uri('bu/view/' . $bu_info->id . '/forecast');
    }
} else if ($tab == "actuals") {
    $card = "bg-success";
    $icon = "check-square";
    if (property_exists($bu_info, "actuals_received")) {
        $value = to_currency($bu_info->actuals_received, $bu_info->currency_symbol);
    }
    if ($view_type == "bu_dashboard") {
        $link = get_uri('forecast_actuals/index');
    } else {
        $link = get_uri('bu/view/' . $bu_info->id . '/actuals');
    }
} else if ($tab == "due") {
    $card = "bg-coral";
    $icon = "dollar-sign";
    if (property_exists($bu_info, "forecast_value")) {
        $value = to_currency(ignor_minor_value($bu_info->forecast_value - $bu_info->actuals_received), $bu_info->currency_symbol);
    }
    if ($view_type == "bu_dashboard") {
        $link = get_uri('forecast/index');
    } else {
        $link = get_uri('bu/view/' . $bu_info->id . '/forecast');
    }
}
?>

<a href="<?php echo $link; ?>" class="white-link">
    <div class="card dashboard-icon-widget">
        <div class="card-body">
            <div class="widget-icon <?php echo $card ?>">
                <i data-feather="<?php echo $icon; ?>" class="icon"></i>
            </div>
            <div class="widget-details">
                <h1><?php echo $value; ?></h1>
                <span><?php echo app_lang($tab); ?></span>
            </div>
        </div>
    </div>
</a>