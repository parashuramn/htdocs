<?php
if($this->layout == "ajax") {
App::import('Model', 'Statement');
$statementModel = new Statement();
$count = $statementModel->find('count', array("fields" => "DISTINCT Statement.objectid", "conditions" => array("grouping_ids LIKE CONCAT('%', objectid, '%')")));
}
else
$count = "-";
echo $this->element("dashboard", array("icon" => "icon-pedestrian", "color" => @$color, "desc" => "Group Level Activities", "type" => "groupactivities", "value" => $count, "url"	=> Router::url(array("controller" => "Reports", "action" => "activities", "sort" => "no_of_statements", "direction" => "desc"))."?object_is_group=1"));