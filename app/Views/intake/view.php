<div class="page-title clearfix no-border no-border-top-radius no-bg">
    <h1>
        <?php echo app_lang('intake_details') . " - " . $intake_info->bu_name ?> 
    </h1>
    <?php echo modal_anchor(get_uri("intake/make_bu_modal_form/") . $intake_info->id, "<i data-feather='briefcase' class='icon-16'></i> " . app_lang('make_bu'), array("class" => "btn btn-primary float-end mr15", "title" => app_lang('make_bu'))); ?>
</div>

<div id="page-content" class="clearfix">
    <ul data-bs-toggle="ajax-tab" class="nav nav-tabs scrollable-tabs no-border-top-radius" role="tablist">
        <li><a  role="presentation" href="<?php echo_uri("intake/contacts/" . $intake_info->id); ?>" data-bs-target="#intake-contacts"> <?php echo app_lang('contacts'); ?></a></li>
        <li><a  role="presentation" href="<?php echo_uri("intake/bu_info_tab/" . $intake_info->id); ?>" data-bs-target="#intake-info"> <?php echo app_lang('intake_info'); ?></a></li>

        <?php if ($show_ticket_info) { ?>
            <li><a  role="presentation" href="<?php echo_uri("intake/tickets/" . $intake_info->id); ?>" data-bs-target="#intake-tickets"> <?php echo app_lang('tickets'); ?></a></li>
        <?php } ?>
        <?php if ($show_note_info) { ?>
            <li><a  role="presentation" href="<?php echo_uri("intake/notes/" . $intake_info->id); ?>" data-bs-target="#intake-notes"> <?php echo app_lang('notes'); ?></a></li>
        <?php } ?>
        <li><a  role="presentation" href="<?php echo_uri("intake/files/" . $intake_info->id); ?>" data-bs-target="#intake-files"><?php echo app_lang('files'); ?></a></li>

        <?php if ($show_event_info) { ?>
            <li><a  role="presentation" href="<?php echo_uri("intake/events/" . $intake_info->id); ?>" data-bs-target="#intake-events"> <?php echo app_lang('events'); ?></a></li>
        <?php } ?>
    </ul>
    <div class="tab-content">
        <div role="tabpanel" class="tab-pane fade" id="intake-projects"></div>
        <div role="tabpanel" class="tab-pane fade" id="intake-files"></div>
        <div role="tabpanel" class="tab-pane fade" id="intake-info"></div>
        <div role="tabpanel" class="tab-pane fade" id="intake-contacts"></div>
        <div role="tabpanel" class="tab-pane fade" id="intake-tickets"></div>
        <div role="tabpanel" class="tab-pane fade" id="intake-notes"></div>
        <div role="tabpanel" class="tab-pane" id="intake-events" style="min-height: 300px"></div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {

        var tab = "<?php echo $tab; ?>";
        if (tab === "info") {
            $("[data-bs-target='#intake-info']").trigger("click");
        }

    });
</script>
