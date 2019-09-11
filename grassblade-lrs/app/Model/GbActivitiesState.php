<?php
App::uses('AppModel', 'Model');
/**
 * GbActivitiesState Model
 *
 */
class GbActivitiesState extends AppModel {

/**
 * Use table
 *
 * @var mixed False or table name
 */
	public $useTable = 'gb_activities_state';

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'stateId';

	public function Process($Input) {
	    switch($Input->Method()) {
			case "GET": 
						$this->process_get($Input);
						break;
			case "PUT": 
						$this->process_put($Input);
						break;
			case "POST": 
						$this->process_post($Input);
						break;
			case "DELETE": 
						$this->process_delete($Input);
						break;
			default:
				$Output = new Output();
				$Output->end("INVALID_REQUEST_METHOD");
		}
		exit;
	}
	public function process_get($Input) {
		$state = $this->process_input($Input);
		$Output = new Output();
		
		if(!empty($state["stateId"])) {
			$found_state = $this->find_state($state);
			$found_state = modified("after_get", $found_state, "activities/state", $Input);		
			$return = @$found_state["content"];
			
			if(!empty($found_state["content_type"]))
			header('Content-type: '.$found_state["content_type"]);

	        $Output->send(200, $return);
		}
		else
		{
			$found_states = $this->find_state($state, true);
			$states = array();
			foreach($found_states as $state)
				$states[] = $state;

			$states = modified("after_get", $states, "activities/state", $Input);		
			$return = json_encode($states);
			$Output->send(200, $return);
		}
	}
	public function process_put($Input) {
		$Output = new Output();
		$state = $this->process_input($Input);

		if(empty($state["stateId"])) {
			$Output->send(400, "Error Bad Request: stateId is required.");
		}
		
		$found_state = $this->find_state($state);
		if(!empty($found_state["id"])) {
			$state["id"] = $found_state["id"];
       		$state["modified"] = date("Y-m-d H:i:s");
        }
        else
       		$state["created"] = $state["modified"] = date("Y-m-d H:i:s");

        if(empty($state["content_type"]))
                $state["content_type"] = "";

        $state = modified("before_save", $state, "activities/state", $Input);

		if($this->save(array("GbActivitiesState" => $state)))
		{
		    $state = modified("after_save", $state, "activities/state", $Input);
			$Output->send(204, "");
		}
		else
			CakeLog::debug($this->validationErrors);
	
	}
	public function process_post($Input) {
		$Output = new Output();
		
		$state = $this->process_input($Input);
		
		if(empty($state["stateId"])) {
			$Output->send(400, "Error Bad Request: stateId is required.");
		}
		
		$found_state = $this->find_state($state);
		
		if(strpos($state["content_type"], "application/json") !== false && strpos($found_state["content_type"], "application/json") !== false)
		{
			$content = (object) ((array) json_decode($state["content"]) + (array) json_decode($found_state["content"]));
			$state["content"] = json_encode($content);
		}
		
		if(!empty($found_state["id"]))
			$state["id"] = $found_state["id"];
        
        if(empty($state["content_type"]))
                $state["content_type"] = "";
            
        $state = modified("before_save", $state, "activities/state", $Input);
		if($this->save(array("GbActivitiesState" => $state)))
		{
		    $state = modified("after_save", $state, "activities/state", $Input);
			$Output->send(204, "");
		}
		else
			CakeLog::debug($this->validationErrors);
	}
	public function process_delete($Input) {
		$state = $this->process_input($Input);
		$Output = new Output();
		
		$state = modified("before_delete", $state, "activities/state", $Input);
		if(!empty($state["stateId"])) {
			$found_state = $this->find_state($state);
			if(!empty($found_state["id"]))
				$this->delete($found_state["id"]);
				
			$Output->send(204, "");
		}
		else
		{
			$found_states = $this->find_state($state, true);
			foreach($found_states as $state)
				$this->delete($state["id"]);
			
			$Output->send(204, "");
		}
	}
	public function process_input($Input) {
		$params = $Input->Params();
		$Output = new Output();
		$agent = !empty($params["agent"])? json_decode($params["agent"]):json_decode(@$params["actor"]);
		$verify_agent = verify_agent($agent);
		
		if(!empty($verify_agent))
			$Output->send(400, "Error:".$verify_agent);
		
		$state["id"] = null;	
		$state["stateId"] = @$params["stateId"];
		$state["agent"] = json_encode($agent);
		$state["activityId"] = @$params["activityId"];
		$state["registration"] = @$params["registration"];
		$state["content"] = @$Input->content;
		$state["content_type"] = @$Input->headers["Content-Type"];
		if(!empty($params["since"]))
		$profile["since"] = @$params["since"];
		
		if(empty($state["activityId"])) {
			$Output->send(400, "Error Bad Request: activityId is required.");
		}
		
		if(empty($state["agent"])) {
			$Output->send(400, "Error Bad Request: agent is required.");
		}
		
		/*if(($Input->Method() == "PUT" || $Input->Method() == "POST") && strpos($state["content_type"], "application/json") !== false)
		{
			$content = json_decode($state["content"]);
			if(is_null($content))
				$Output->send(400, "Error Bad Request: Invalid JSON: ".$state["content"]);
		}*/
		
		if(!empty($agent->name)) {
			$state['agent_name'] = is_array($agent->name)? $agent->name[0]:$agent->name;
		}
		if(!empty($agent->mbox)){
			$mbox = is_array($agent->mbox)? $agent->mbox[0]:$agent->mbox;
			$email = str_replace("mailto:", "", $mbox);
		
			if(!empty($mbox))
				$state['agent_mbox'] = $email;
			$state['agent_id'] = $email;
		}
		if(!empty($agent->mbox_sha1sum)) {
			$state['agent_mbox_sha1sum'] = $agent->mbox_sha1sum;
			$state['agent_id'] = $agent->mbox_sha1sum;
		}
		if(!empty($agent->openid)) {
			$state['agent_openid'] = $agent->openid;
			$state['agent_id'] = $agent->openid;
		}
		if(!empty($agent->account->homePage)) {
			$state['agent_account_homePage'] = $agent->account->homePage;
			$state['agent_id'] = $agent->account->homePage;
		}
		if(!empty($agent->account->name)) {
			$state['agent_account_name'] = $agent->account->name;
			$state['agent_id'] = @$state['agent_id']."/".$agent->account->name;
		}
		return $state;
	}
	public function find_state($state, $multi = false) {
		foreach($state as $key=>$value) {
			if(empty($state[$key]) || !in_array($key, array("stateId", "activityId", "registration","agent_id", "agent_mbox", "agent_mbox_sha1sum", "agent_openid", "agent_account_homePage", "agent_account_name", "since")))
				unset($state[$key]);
			else if($key == "since" && !empty($state[$key])) {
				$state["created > "] = date('Y-m-d H:i:s', strtotime($state[$key])); 
				unset($state[$key]);
			}
		}

		$state = modified("before_get", $state, "activities/state", $multi);		
		if(empty($multi)) {
			$found = $this->find('first', array("conditions" => $state));
			if(!empty($found["GbActivitiesState"]))
				return $found["GbActivitiesState"];
		}
		else
		{
			$found = $this->find('list', array("conditions" => $state));
			return $found;
		}
	}
}
