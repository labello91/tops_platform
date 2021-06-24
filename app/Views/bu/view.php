<div class="page-title clearfix no-border no-border-top-radius no-bg">
    <h1>
        <?php echo app_lang('bu_details') . " - " . $bu_info->bu_name ?>
        <span id="star-mark">
            <?php
            if ($is_starred) {
                echo view('bu/star/starred', array("bu_id" => $bu_info->id));
            } else {
                echo view('bu/star/not_starred', array("bu_id" => $bu_info->id));
            }
            ?>
        </span>
    </h1>
</div>

<div id="page-content" class="clearfix">

    <?php
    if ($bu_info->intake_status_id) {
        echo view("bu/intake_information");
    }
    ?>

    <div class="bu-widget-section">
        <?php echo view("bu/info_widgets/index"); ?>
    </div>

    <ul id="bu-tabs" data-bs-toggle="ajax-tab" class="nav nav-tabs scrollable-tabs no-border-top-radius" role="tablist">
        <li><a  role="presentation" href="<?php echo_uri("bu/contacts/" . $bu_info->id); ?>" data-bs-target="#bu-contacts"> <?php echo app_lang('contacts'); ?></a></li>
        <li><a  role="presentation" href="<?php echo_uri("bu/bu_info_tab/" . $bu_info->id); ?>" data-bs-target="#bu-info"> <?php echo app_lang('bu_info'); ?></a></li>
        <li><a  role="presentation" href="<?php echo_uri("bu/projects/" . $bu_info->id); ?>" data-bs-target="#bu-projects"><?php echo app_lang('projects'); ?></a></li>

        <?php if ($show_forecast_info) { ?>
            <li><a  role="presentation" href="<?php echo_uri("bu/forecast/" . $bu_info->id); ?>" data-bs-target="#bu-forecast"> <?php echo app_lang('forecast'); ?></a></li>
            <li><a  role="presentation" href="<?php echo_uri("bu/actuals/" . $bu_info->id); ?>" data-bs-target="#bu-actuals"> <?php echo app_lang('actuals'); ?></a></li>
        <?php } ?>
        <?php if ($show_ticket_info) { ?>
            <li><a  role="presentation" href="<?php echo_uri("bu/tickets/" . $bu_info->id); ?>" data-bs-target="#bu-tickets"> <?php echo app_lang('tickets'); ?></a></li>
        <?php } ?>
        <?php if ($show_note_info) { ?>
            <li><a  role="presentation" href="<?php echo_uri("bu/notes/" . $bu_info->id); ?>" data-bs-target="#bu-notes"> <?php echo app_lang('notes'); ?></a></li>
        <?php } ?>
        <li><a  role="presentation" href="<?php echo_uri("bu/files/" . $bu_info->id); ?>" data-bs-target="#bu-files"><?php echo app_lang('files'); ?></a></li>

        <?php if ($show_event_info) { ?>
            <li><a  role="presentation" href="<?php echo_uri("bu/events/" . $bu_info->id); ?>" data-bs-target="#bu-events"> <?php echo app_lang('events'); ?></a></li>
        <?php } ?>
    </ul>
    <div class="tab-content">
        <div role="tabpanel" class="tab-pane fade" id="bu-projects"></div>
        <div role="tabpanel" class="tab-pane fade" id="bu-files"></div>
        <div role="tabpanel" class="tab-pane fade" id="bu-info"></div>
        <div role="tabpanel" class="tab-pane fade" id="bu-contacts"></div>
        <div role="tabpanel" class="tab-pane fade" id="bu-forecast"></div>
        <div role="tabpanel" class="tab-pane fade" id="bu-actuals"></div>
        <div role="tabpanel" class="tab-pane fade" id="bu-orders"></div>
        <div role="tabpanel" class="tab-pane fade" id="bu-tickets"></div>
        <div role="tabpanel" class="tab-pane fade" id="bu-notes"></div>
        <div role="tabpanel" class="tab-pane" id="bu-events" style="min-height: 300px"></div>
        <div role="tabpanel" class="tab-pane fade" id="bu-actuals"></div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {

        setTimeout(function () {
            var tab = "<?php echo $tab; ?>";
            if (tab === "info") {
                $("[data-bs-target='#bu-info']").trigger("click");
            } else if (tab === "projects") {
                $("[data-bs-target='#bu-projects']").trigger("click");
            } else if (tab === "forecast") {
                $("[data-bs-target='#bu-forecast']").trigger("click");
            } else if (tab === "actuals") {
                $("[data-bs-target='#bu-actuals']").trigger("click");
            }
        }, 210);

    });
</script>