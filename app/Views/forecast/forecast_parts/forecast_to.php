<div><b><?php echo app_lang("forecast_to"); ?></b></div>
<div class="b-b" style="line-height: 2px; border-bottom: 1px solid #f2f4f6;"> </div>
<div style="line-height: 3px;"> </div>
<strong><?php echo $bu_info->bu_name; ?> </strong>
<div style="line-height: 3px;"> </div>
<span class="forecast-meta text-default" style="font-size: 90%; color: #666;">
    <?php if ($bu_info->name || $bu_info->vat_number || (isset($bu_info->custom_fields) && $bu_info->custom_fields)) { ?>
        <div><?php echo nl2br($bu_info->name); ?>
            <?php if ($bu_info->contacts) { ?>
                <br /><?php echo $bu_info->contacts; ?>
            <?php } ?>
            <?php if ($bu_info->country) { ?>
                <br /><?php echo $bu_info->country; ?>
            <?php } ?>
            <?php
            if (isset($bu_info->custom_fields) && $bu_info->custom_fields) {
                foreach ($bu_info->custom_fields as $field) {
                    if ($field->value) {
                        echo "<br />" . $field->custom_field_title . ": " . view("custom_fields/output_" . $field->custom_field_type, array("value" => $field->value));
                    }
                }
            }
            ?>


        </div>
    <?php } ?>
</span>