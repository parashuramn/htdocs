<?php
if($this->layout == "ajax") {
App::import('Model', 'Statement');
$statementModel = new Statement();
$count = $statementModel->find('count', array("fields" => "DISTINCT Statement.agent_id"));
}
else
$count = "-";
echo $this->element("dashboard", array("icon" => "fa-users", "color" => @$color, "desc" => "Learners", "type" => "agents", "value" => $count, "url" => Router::url(array("controller" => "Reports", "action" => "agents", "sort" => "no_of_statements", "direction" => "desc"))));