<?php
function statement_hooks_log($message) {
	//CakeLog::write('statement_hooks', $message);
}
function statement_hooks($statement_array, $Context = null, $triggers = null) {
	set_time_limit(300);
	statement_hooks_log('statement_hooks');
	statement_hooks_log(  print_r($statement_array, true));
	if(empty($triggers))
	$triggers = get_triggers();
	foreach ($triggers as $k => $trigger) {
		if( empty($trigger["status"]) )
			continue;
		
		$completion_criterions = unserialize($trigger["criterion"]);
		if($triggers[$k]["match"] = completion_criterion_match($completion_criterions, $statement_array))
		{
			if($triggers[$k]["match"] !== true)
				continue;
			statement_hooks_log(  "completion_criterion_match");
			statement_hooks_log(  print_r($trigger, true));
			statement_hooks_log(  print_r($statement_array, true));

			if($trigger["type"] == "completion")
			$triggers[$k]["return"] = process_completion_trigger($trigger, $statement_array);
			else 
			if($trigger["type"] == "post_to_url_post")
			$triggers[$k]["return"] = process_post_to_url_post_trigger($trigger, $statement_array);
			else 
			if($trigger["type"] == "post_to_url_get")
			$triggers[$k]["return"] = process_post_to_url_get_trigger($trigger, $statement_array);
		}
	}
	return $triggers;
}
function completion_criterion_match($completion_criterions, $statement_array) {

	foreach($completion_criterions as $key => $value) {
		if(!empty($value) && !empty($key))
		if($statement_array[trim($key)] != trim($value)) {
			return $statement_array[trim($key)]." != ".trim($value);
		}
	}
	return true;	
}
function process_completion_trigger($trigger, $statement_array) {
	statement_hooks_log(  "process_completion_trigger");
	$target = unserialize(@$trigger["target"]);
	$url = @$target["url"];
	if(empty($url) || !filter_var($url, FILTER_VALIDATE_URL)) {
		$url = get_option("siteurl");
	}
	statement_hooks_log("URL:".$url);
	
	if(!empty($url) && filter_var($url, FILTER_VALIDATE_URL)) {
		$data = array(
				"grassblade_completion_tracking" => 1,
				"grassblade_trigger" => 1,
				"statement"	=> $statement_array["statement"],
				"objectid"	=> $statement_array["objectid"],
				"verb_id"	=> $statement_array["verb_id"],
				"agent_id"	=> $statement_array["agent_id"]
			);
		//$url = $url."?grassblade_completion_tracking=1&statement=".json_encode($statement_array);
		$error_log_id = trigger_error_log_start($trigger, $url, $statement_array, $data, $request_method = "POST");
		$return = post_to_url_post($url, $data, $return_info = true);
		trigger_error_log_update($error_log_id, $return);

		statement_hooks_log(  "POST RETURN: ".print_r($return, true));
		return $return;
	}
	
	return false;
}
function process_post_to_url_post_trigger($trigger, $statement_array) {
	statement_hooks_log(  "process_post_to_url_post_trigger");
	$target = unserialize(@$trigger["target"]);
	$url = @$target["url"];
	statement_hooks_log(  "URL:".$url);

	if(!empty($url) && filter_var($url, FILTER_VALIDATE_URL)) {
		$data = array(
				"grassblade_trigger" => 1,
				"statement"	=> $statement_array["statement"],
				"objectid"	=> $statement_array["objectid"],
				"verb_id"	=> $statement_array["verb_id"],
				"agent_id"	=> $statement_array["agent_id"]
			);
		$error_log_id = trigger_error_log_start($trigger, $url, $statement_array, $data, $request_method = "POST");
		$return = post_to_url_post($url, $data, $return_info = true);
		trigger_error_log_update($error_log_id, $return);

		statement_hooks_log(  "POST RETURN: ".print_r($return, true));
		return $return;
	}
	
	return false;
}
function process_post_to_url_get_trigger($trigger, $statement_array) {
	statement_hooks_log(  "process_post_to_url_get_trigger");
	$target = unserialize(@$trigger["target"]);
	$url = @$target["url"];
	statement_hooks_log(  "URL:".$url);

	if(!empty($url)) {
		$data = array(
				"grassblade_trigger" => 1,
				"statement"	=> $statement_array["statement"],
				"objectid"	=> $statement_array["objectid"],
				"verb_id"	=> $statement_array["verb_id"],
				"agent_id"	=> $statement_array["agent_id"]
			);
		$error_log_id = trigger_error_log_start($trigger, $url, $statement_array, $data, $request_method = "GET");
		$return = post_to_url_get($url, $data, $return_info = true);
		trigger_error_log_update($error_log_id, $return);

		statement_hooks_log(  "GET RETURN: ".print_r($return, true));
		return $return;
	}
	
	return false;
}
function get_triggers() {
	App::import("Model", "Trigger");
	$TriggerM = new Trigger();
	$triggers_list = $TriggerM->find("all");

	$triggers = array();
	foreach ($triggers_list as $trigger) {
		$triggers[] = $trigger["Trigger"];
	}
	return $triggers;
}
function get_option($name) {
	global $gbdb;
	App::import("Model", "Statement");
	$Model = new Statement();
	$table = $Model->tablePrefix."options";
	if($gbdb->table_exists("options")) {
			$sql = "SELECT option_value FROM ".$table." WHERE option_name = '".$name."' LIMIT 1";
		//CakeLog::write("statement_hooks", $sql);
			$result = $Model->query($sql);
		//CakeLog::write("statement_hooks", print_r($result, true));
			if(!empty($result[0][$table]["option_value"])) {
				return $result[0][$table]["option_value"];
			}
	}
	return '';	
}
function trigger_error_log_start($trigger, $url, $statement_array, $data, $request_method) {
	App::import('Model', 'ErrorLog');
	$ErrorLog = new ErrorLog();
	return $ErrorLog->add("Trigger", array(
			"user"			=> $statement_array["agent_id"],
			"objectid"		=> $statement_array["objectid"],
			"statement_id" 	=> $statement_array["statement_id"],
			"url"			=> $url,
			"request_method" => $request_method,
			"data" => array(
					"trigger" => $trigger,
					$request_method => $data,
				),
			"error_code"	=> 0,
			"response"		=> "",
		), 2);
}
function trigger_error_log_update($id, $return, $error_msg = '', $error_code = '') {
	App::import('Model', 'ErrorLog');
	$ErrorLog = new ErrorLog();
	if(is_array($return) || is_object($return)) {
		$status = 0;
		if(is_array($return)) {
			if(!empty($return["return"]) && strlen($return["return"]) > 3000)
				$return["return"] = substr($return["return"], 0, 3000);
			if(!empty($return["error"]) && strlen($return["error"]) > 3000)
				$return["error"] = substr($return["error"], 0, 3000);

			$info = @$return["info"];
			if(empty($error_msg))
			{
				if(!empty( $return["error"] ) && is_string($return["error"]))
					$error_msg = $return["error"];
				else
					$error_msg = @$return["return"];
			}
			if(!empty($info["http_code"])) {
				$error_code = $info["http_code"];
				if($error_code == 200) {
					if(strpos($error_msg, "Completion Failed") == false)
					$status = 1;
				}
			}
		}
		$return = json_encode($return);
	}
	else
		$status = 1;

	if(strlen($error_msg) > 3000)
		$error_msg = substr($error_msg, 0, 3000);

	return $ErrorLog->update($id, array(
			"response" => $return,
			"error_msg" => $error_msg,
			"error_code" => $error_code,
		), $status);
}