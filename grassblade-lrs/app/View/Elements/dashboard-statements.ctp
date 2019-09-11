<?php
if($this->layout == "ajax") {
App::import('Model', 'Statement');
$statementModel = new Statement();
$count = $statementModel->find('count');
}
else
$count = "-";
echo $this->element("dashboard", array("icon" => "fa-tasks", "color" => @$color, "desc" => "Statements", "type" => "statements", "value" => $count, "url" => Router::url(array("controller" => "Reports", "action" => "index"))));