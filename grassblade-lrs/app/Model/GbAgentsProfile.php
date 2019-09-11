<?php
App::uses('AppModel', 'Model');
/**
 * GbAgentsProfile Model
 *
 */
class GbAgentsProfile extends AppModel {

/**
 * Use table
 *
 * @var mixed False or table name
 */
	public $useTable = 'gb_agents_profile';

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'profileId';

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
		$profile = $this->process_input($Input);
		$Output = new Output();
		
		if(!empty($profile["profileId"])) {
			$found_profile = $this->find_profile($profile);
			$found_profile = modified("after_get", $found_profile, "agents/profile", $Input);		
			$return = @$found_profile["content"];
			
			if(!empty($found_profile["content_type"]))
			header('Content-type: '.$found_profile["content_type"]);
			
			$Output->send(200, $return);
		}
		else
		{
			$found_profiles = $this->find_profile($profile, true);
			$profiles = array();
			foreach($found_profiles as $profile)
				$profiles[] = $profile;
			
			$profiles = modified("after_get", $profiles, "agents/profile", $Input);		
			$return = json_encode($profiles);
			$Output->send(200, $return);
		}
	}
	public function process_put($Input) {
		$Output = new Output();
		$profile = $this->process_input($Input);
		
		if(empty($profile["profileId"])) {
			$Output->send(400, "Error Bad Request: profileId is required.");
		}
		
		$found_profile = $this->find_profile($profile);
		if(!empty($found_profile["id"])) {
			$profile["id"] = $found_profile["id"];
       		$profile["modified"] = date("Y-m-d H:i:s");
        }
        else
       		$profile["created"] = $profile["modified"] = date("Y-m-d H:i:s");

		if(empty($profile["content_type"]))
			$profile["content_type"] = "";

        $profile = modified("before_save", $profile, "agents/profile", $Input);
		if($this->save(array("GbAgentsProfile" => $profile)))
		{
		    $profile = modified("after_save", $profile, "agents/profile", $Input);
			$Output->send(204, "");
		}
		else
			CakeLog::debug($this->validationErrors);
	
	}
	public function process_post($Input) {
		$Output = new Output();
		
		$profile = $this->process_input($Input);
		
		if(empty($profile["profileId"])) {
			$Output->send(400, "Error Bad Request: profileId is required.");
		}
		
		$found_profile = $this->find_profile($profile);
		
		if(strpos($profile["content_type"], "application/json") !== false && strpos($found_profile["content_type"], "application/json") !== false)
		{
			$content = (object) ((array) json_decode($profile["content"]) + (array) json_decode($found_profile["content"]));
			$profile["content"] = json_encode($content);
		}
		
		if(!empty($found_profile["id"]))
			$profile["id"] = $found_profile["id"];

		if(empty($profile["content_type"]))
			$profile["content_type"] = "";
		
        $profile = modified("before_save", $profile, "agents/profile", $Input);
		if($this->save(array("GbAgentsProfile" => $profile)))
		{
		    $profile = modified("after_save", $profile, "agents/profile", $Input);
			$Output->send(204, "");
		}
		else
			CakeLog::debug($this->validationErrors);
	}
	public function process_delete($Input) {
		$profile = $this->process_input($Input);
		$Output = new Output();

		$profile = modified("before_delete", $profile, "agents/profile", $Input);		
		if(!empty($profile["profileId"])) {
			$found_profile = $this->find_profile($profile);
			if(!empty($found_profile["id"]))
				$this->delete($found_profile["id"]);
				
			$Output->send(204, "");
		}
		else
		{
			$found_profiles = $this->find_profile($profile, true);
			foreach($found_profiles as $profile)
				$this->delete($profile["id"]);
			
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
		
		$profile["id"] = null;	
		$profile["profileId"] = @$params["profileId"];
		$profile["agent"] = json_encode($agent);
		$profile["content"] = @$Input->content;
		$profile["content_type"] = @$Input->headers["Content-Type"];
		if(!empty($params["since"]))
		$profile["since"] = @$params["since"];
		
		if(empty($profile["agent"])) {
			$Output->send(400, "Error Bad Request: agent is required.");
		}
		
		/*if(($Input->Method() == "PUT" || $Input->Method() == "POST") && strpos($profile["content_type"], "application/json") !== false)
		{
			$content = json_decode($profile["content"]);
			if(is_null($content))
				$Output->send(400, "Error Bad Request: Invalid JSON: ".$profile["content"]);
		}*/
		
		if(!empty($agent->name)) {
			$profile['agent_name'] = is_array($agent->name)? $agent->name[0]:$agent->name;
		}
		if(!empty($agent->mbox)){
			$mbox = is_array($agent->mbox)? $agent->mbox[0]:$agent->mbox;
			$email = str_replace("mailto:", "", $mbox);
		
			if(!empty($mbox))
				$profile['agent_mbox'] = $email;
			$profile['agent_id'] = $email;
		}
		if(!empty($agent->mbox_sha1sum)) {
			$profile['agent_mbox_sha1sum'] = $agent->mbox_sha1sum;
			$profile['agent_id'] = $agent->mbox_sha1sum;
		}
		if(!empty($agent->openid)) {
			$profile['agent_openid'] = $agent->openid;
			$profile['agent_id'] = $agent->openid;
		}
		if(!empty($agent->account->homePage)) {
			$profile['agent_account_homePage'] = $agent->account->homePage;
			$profile['agent_id'] = $agent->account->homePage;
		}
		if(!empty($agent->account->name)) {
			$profile['agent_account_name'] = $agent->account->name;
			$profile['agent_id'] = @$profile['agent_id']."/".$agent->account->name;
		}
		
		return $profile;
	}
	public function find_profile($profile, $multi = false) {
		foreach($profile as $key=>$value) {
			if(empty($profile[$key]) || !in_array($key, array("profileId", "agent_mbox", "agent_mbox_sha1sum", "agent_openid", "agent_account_homePage", "agent_account_name", "since")))
				unset($profile[$key]);
			else if($key == "since" && !empty($profile[$key])) {
				$profile["created > "] = date('Y-m-d H:i:s', strtotime($profile[$key])); 
				unset($profile[$key]);
			}
		}	
		$profile = modified("before_get", $profile, "agents/profile", $multi);		
		if(empty($multi)) {
			$found = $this->find('first', array("conditions" => $profile));
			if(!empty($found["GbAgentsProfile"]))
				return $found["GbAgentsProfile"];
		}
		else
		{
			$found = $this->find('list', array("conditions" => $profile));
			return $found;
		}
	}
}
