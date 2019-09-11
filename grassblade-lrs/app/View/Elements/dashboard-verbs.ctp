<?php
App::import('Model', 'Statement');
$statementModel = new Statement();

$last_statement_id = $statementModel->find("first", array("order" => "ID DESC") );
$last_statement_id = (int) @$last_statement_id["Statement"]["id"];
if($last_statement_id < 100000)
	$count = $statementModel->find('count', array("fields" => "DISTINCT Statement.verb_id"));
else
{
	$user_id = User::get("id");
	$config_key = "verbs.".$user_id;
	$stored_verbs = grassblade_config_get($config_key, array());
	$count = count($stored_verbs);
}

echo $this->element("dashboard", array("icon" => 'fa-mortar-board', "color" => @$color, "desc" => "Verbs", "value" => $count, "url" => Router::url(array("controller" => "Reports", "action" => "verbs", "sort" => "no_of_statements", "direction" => "desc"))));

