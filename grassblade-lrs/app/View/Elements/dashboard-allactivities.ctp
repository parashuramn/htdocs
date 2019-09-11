<?php
if($this->layout == "ajax") {
App::import('Model', 'Statement');
$statementModel = new Statement();
$count = $statementModel->find('count', array("fields" => "DISTINCT Statement.objectid"));
}
else
$count = "-";
echo $this->element("dashboard", array("icon" => "icon-pedestrian", "color" => @$color, "desc" => "All Activities", "type" => "allactivities", "value" => $count,"url"	=> Router::url(array("controller" => "Reports", "action" => "activities", "sort" => "no_of_statements", "direction" => "desc"))));