<?php
App::import('Model', 'User');
$userModel = new User();
$count = $userModel->find('count');
echo $this->element("dashboard", array("icon" => "fa-user", "color" => @$color, "desc" => "Managers", "value" => $count, "url" => Router::url(array("controller" => "Users"))));