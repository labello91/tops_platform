<?php

if ($notification->task_id && $notification->task_title) {
    echo "\n*" . app_lang("task") . ":* #$notification->task_id - " . $notification->task_title;
}

if ($notification->actuals_forecast_id) {
    echo "\n" . to_currency($notification->actuals_amount, $notification->bu_currency_symbol) . "  -  " . get_forecast_id($notification->actuals_forecast_id);
}

if ($notification->ticket_id && $notification->ticket_title) {
    echo "\n" . get_ticket_id($notification->ticket_id) . " - " . $notification->ticket_title;
}

if ($notification->project_comment_id && $notification->project_comment_title && !strpos($notification->project_comment_title, "</a>")) {
    echo "\n*" . app_lang("comment") . ":* " . convert_mentions($notification->project_comment_title, false);
}

if ($notification->project_file_id && $notification->project_file_title) {
    echo "\n*" . app_lang("file") . ":* " . remove_file_prefix($notification->project_file_title);
}

if ($notification->project_id && $notification->project_title) {
    echo "\n*" . app_lang("project") . ":* " . $notification->project_title;
}

if ($notification->estimate_id) {
    echo "\n" . get_estimate_id($notification->estimate_id);
}

if ($notification->event_title) {
    echo "\n*" . app_lang("event") . ":* " . $notification->event_title;
}

if ($notification->announcement_title) {
    echo "\n*" . app_lang("title") . ":* " . $notification->announcement_title;
}
