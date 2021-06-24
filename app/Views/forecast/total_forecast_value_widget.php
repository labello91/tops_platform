<?php
$card = "";
$icon = "";
$value = "";
$lang = "";
$link = "";

if ($type == "forecast") {
    $lang = app_lang("forecast_value");
    $card = "bg-primary";
    $icon = "file-text";
    $value = to_currency($forecast_info->forecast_total);
    $link = get_uri('forecast/index');
} else if ($type == "actuals") {
    $lang = app_lang("actuals");
    $card = "bg-success";
    $icon = "check-square";
    $value = to_currency($forecast_info->actuals_total);
    $link = get_uri('forecast_actuals/index');
} else if ($type == "due") {
    $lang = app_lang("due");
    $card = "bg-coral";
    $icon = "dollar-sign";
    $value = to_currency(ignor_minor_value($forecast_info->due));
    $link = get_uri('forecast/index');
} else if ($type == "draft") {
    $lang = app_lang("draft_forecast_total");
    $card = "bg-orange";
    $icon = "file-text";
    $value = to_currency($forecast_info->draft_total);
    $link = get_uri('forecast/index');
}
?>

<a href="<?php echo $link; ?>" class="white-link">
    <div class="card  dashboard-icon-widget">
        <div class="card-body ">
            <div class="widget-icon <?php echo $card ?>">
                <i data-feather="<?php echo $icon; ?>" class="icon"></i>
            </div>
            <div class="widget-details">
                <h1><?php echo $value; ?></h1>
                <span><?php echo $lang; ?></span>
            </div>
        </div>
    </div>
</a>