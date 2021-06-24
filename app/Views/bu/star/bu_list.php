<div class="list-group">
    <?php
    if (count($bu)) {
        foreach ($bu as $bu) {

            $icon = "briefcase";

            $title = "<i data-feather='$icon' class='icon-16 mr10'></i> " . $bu->bu_name;
            echo anchor(get_uri("bu/view/" . $bu->id), $title, array("class" => "dropdown-item"));
        }
    } else {
        ?>
        <div class='list-group-item'>
            <?php echo app_lang("empty_starred_bu"); ?>              
        </div>
    <?php } ?>
</div>