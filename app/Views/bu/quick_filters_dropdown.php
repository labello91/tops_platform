<?php

$quick_filters_dropdown = array(
    array("id" => "", "text" => "- " . app_lang("quick_filters") . " -"),
    array("id" => "has_open_projects", "text" => app_lang("has_open_projects")),
    array("id" => "has_completed_projects", "text" => app_lang("has_completed_projects")),
    array("id" => "has_any_hold_projects", "text" => app_lang("has_any_hold_projects")),
    array("id" => "has_not_updated_actuals", "text" => app_lang("has_not_updated_actuals")),
    array("id" => "has_overdue_forecast", "text" => app_lang("has_overdue_forecast")),
    array("id" => "has_partially_achieved_actuals", "text" => app_lang("has_partially_achieved_actuals"))
);
echo json_encode($quick_filters_dropdown);
?>