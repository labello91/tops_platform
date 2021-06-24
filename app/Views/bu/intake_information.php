<div class="bg-off-white p15 pt0">
    <span class="font-16"><?php echo app_lang("past_intake_information"); ?></span>

    <div class="mt5">
        <?php if ($bu_info->created_date) { ?>
            <?php echo app_lang("intake_created_at") . ": " . format_to_date($bu_info->created_date, false); ?>
        <?php } ?>
        <?php if ($bu_info->bu_migration_date && is_date_exists($bu_info->bu_migration_date)) { ?>
            <br /><?php echo app_lang("migrated_to_bu_at") . ": " . format_to_date($bu_info->bu_migration_date, false); ?>
        <?php } ?>
        <?php if ($bu_info->last_intake_status) { ?>
            <br /><?php echo app_lang("last_status") . ": " . $bu_info->last_intake_status; ?>
        <?php } ?>
        <?php if ($bu_info->owner_id) { ?>
            <br /><?php echo app_lang("owner") . ": " . get_team_member_profile_link($bu_info->owner_id, $bu_info->owner_name); ?>
        <?php } ?>
    </div>
</div>