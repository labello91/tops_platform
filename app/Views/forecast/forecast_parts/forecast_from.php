<?php
$bu_name = nl2br(get_setting("bu_name"));
$bu_contacts = get_setting("bu_contacts");
?><div><b><?php echo get_setting("bu_name"); ?></b></div>
<div style="line-height: 3px;"> </div>
<span class="forecast-meta text-default" style="font-size: 90%; color: #666;"><?php
    if ($bu_name) {
        echo $bu_name;
    }
    ?>
    <?php if ($bu_contacts) { ?>
        <br /><?php echo app_lang("contacts") . ": " . $bu_contacts; ?>
    <?php } ?>

</span>