<div id="page-content" class="page-wrapper clearfix">
    <?php
    if (count($dashboards) && !get_setting("disable_dashboard_customization_by_bu")) {
        echo view("dashboards/dashboard_header");
    }

    echo announcements_alert_widget();
    ?>
    <div class="">
        <?php echo view("bu/info_widgets/index"); ?>
    </div>

    <?php if (!in_array("projects", $hidden_menu)) { ?>
        <div class="">
            <?php echo view("bu/projects/index"); ?>
        </div>
    <?php } ?>

</div>