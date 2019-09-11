<?php
App::import('Model', 'Trigger');
$triggerModel = new Trigger();
$count = $triggerModel->find('count');
echo $this->element("dashboard", array("icon" => "fa-external-link", "color" => @$color, "desc" => "Triggers", "value" => $count, "url" => Router::url(array("controller" => "Triggers"))));