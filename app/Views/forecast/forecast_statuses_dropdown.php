<?php

$forecast_statuses_dropdown = array(
    array("id" => "", "text" => "- " . app_lang("status") . " -"),
    array("id" => "overdue", "text" => app_lang("overdue")),
    array("id" => "draft", "text" => app_lang("draft")),
    array("id" => "not_achieved", "text" => app_lang("not_achieved")),
    array("id" => "partially_achieved", "text" => app_lang("partially_achieved")),
    array("id" => "fully_achieved", "text" => app_lang("fully_achieved")),
    array("id" => "cancelled", "text" => app_lang("cancelled"))
);
echo json_encode($forecast_statuses_dropdown);
?>