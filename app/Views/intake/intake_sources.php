<?php

$sources = array(array("id" => "", "text" => "- " . app_lang("source") . " -"));
foreach ($intake_sources as $source) {
    $sources[] = array("id" => $source->id, "text" => $source->title);
}

echo json_encode($sources);
?>