<div class="bg-white  p15 no-border m0 rounded-bottom">
    <span class="mr10"><?php echo $forecast_status_label; ?></span>

    <?php echo make_labels_view_data($forecast_info->labels_list, "", true); ?>

    <?php if ($forecast_info->project_id) { ?>
        <span class="ml15"><?php echo app_lang("project") . ": " . anchor(get_uri("projects/view/" . $forecast_info->project_id), $forecast_info->project_title); ?></span>
    <?php } ?>

    <span class="ml15"><?php
        echo app_lang("bu") . ": ";
        echo (anchor(get_uri("bu/view/" . $forecast_info->bu_id), $forecast_info->bu_name));
        ?>
    </span> 

    <span class="ml15"><?php
        echo app_lang("last_email_sent") . ": ";
        echo (is_date_exists($forecast_info->last_email_sent_date)) ? format_to_date($forecast_info->last_email_sent_date, FALSE) : app_lang("never");
        ?>
    </span>
    <?php if ($forecast_info->recurring_forecast_id) { ?>
        <span class="ml15">
            <?php
            echo app_lang("created_from") . ": ";
            echo anchor(get_uri("forecast/view/" . $forecast_info->recurring_forecast_id), get_forecast_id($forecast_info->recurring_forecast_id));
            ?>
        </span>
    <?php } ?>

    <?php if ($forecast_info->cancelled_at) { ?>
        <span class="ml15"><?php echo app_lang("cancelled_at") . ": " . format_to_relative_time($forecast_info->cancelled_at); ?></span>
    <?php } ?>

    <?php if ($forecast_info->cancelled_by) { ?>
        <span class="ml15"><?php echo app_lang("cancelled_by") . ": " . get_team_member_profile_link($forecast_info->cancelled_by, $forecast_info->cancelled_by_user); ?></span>
    <?php } ?>

</div>