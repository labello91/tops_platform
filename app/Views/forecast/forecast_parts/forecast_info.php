<span class="forecast-info-title" style="font-size:20px; font-weight: bold;background-color: <?php echo $color; ?>; color: #fff;">&nbsp;<?php echo get_forecast_id($forecast_info->id); ?>&nbsp;</span>
<div style="line-height: 10px;"></div><?php
if (isset($forecast_info->custom_fields) && $forecast_info->custom_fields) {
    foreach ($forecast_info->custom_fields as $field) {
        if ($field->value) {
            echo "<span>" . $field->custom_field_title . ": " . view("custom_fields/output_" . $field->custom_field_type, array("value" => $field->value)) . "</span><br />";
        }
    }
}
?>
<span><?php echo app_lang("forecast_date") . ": " . format_to_date($forecast_info->forecast_date, false); ?></span><br />
<span><?php echo app_lang("due_date") . ": " . format_to_date($forecast_info->due_date, false); ?></span>