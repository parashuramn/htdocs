<?php
App::uses('AppModel', 'Model');
/**
 * GbActivitiesProfile Model
 *
 */
class GbActivitiesProfile extends AppModel {

/**
 * Use table
 *
 * @var mixed False or table name
 */
	public $useTable = 'gb_activities_profile';

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
			$found_profile = modified("after_get", $found_profile, "activities/profile", $Input);		
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

			$profiles = modified("after_get", $profiles, "activities/profile", $Input);		
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

        $profile = modified("before_save", $profile, "activities/profile", $Input);
		if($this->save(array("GbActivitiesProfile" => $profile)))
		{
		    $profile = modified("after_save", $profile, "activities/profile", $Input);
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

        $profile = modified("before_save", $profile, "activities/profile", $Input);
		if($this->save(array("GbActivitiesProfile" => $profile)))
		{
		    $profile = modified("after_save", $profile, "activities/profile", $Input);
			$Output->send(204, "");
		}
		else
			CakeLog::debug($this->validationErrors);
	}
	public function process_delete($Input) {
		$profile = $this->process_input($Input);
		$Output = new Output();
		
		$profile = modified("before_delete", $profile, "activities/profile", $Input);
		if(!empty($profile["profileId"])) {
			$found_profile = $this->find_profile($profile);
			if(!empty($found_profile["id"]))
				$this->delete($found_profile["id"]);
				
			$Output->send(204, "");
		}
		else
		{
			/*$found_profiles = $this->find_profile($profile, true);
			foreach($found_profiles as $profile)
				$this->delete($profile["id"]);
			$Output->send(204, "");
			*/
			$Output->send(400, "Error Bad Request: profileId is required.");
		}
	}
	public function process_input($Input) {
		$params = $Input->Params();
		$Output = new Output();
		
		$profile["id"] = null;	
		$profile["profileId"] = @$params["profileId"];
		$profile["activityId"] = @$params["activityId"];
		
		if(!empty($params["since"]))
		$profile["since"] = @$params["since"];
		$profile["content"] = @$Input->content;
		$profile["content_type"] = @$Input->headers["Content-Type"];

		if(empty($profile["activityId"])) {
			$Output->send(400, "Error Bad Request: activityId is required.");
		}
		
		if(($Input->Method() == "PUT" || $Input->Method() == "POST") && strpos($profile["content_type"], "application/json") !== false)
		{
			$content = json_decode($profile["content"]);
			if(is_null($content))
				$Output->send(400, "Error Bad Request: Invalid JSON: ".$profile["content"]);
		}
		
		return $profile;
	}
	public function find_profile($profile, $multi = false) {
		//print_r($profile);
		foreach($profile as $key=>$value) {
			if(empty($profile[$key]) || !in_array($key, array("profileId", "activityId", "since")))
				unset($profile[$key]);
			else if($key == "since" && !empty($profile[$key])) {
				$profile["created > "] = date('Y-m-d H:i:s', strtotime($profile[$key])); 
				unset($profile[$key]);
			}
		}
		$profile = modified("before_get", $profile, "activities/profile", $multi);		
		if(empty($multi)) {
			$found = $this->find('first', array("conditions" => $profile));
			if(!empty($found["GbActivitiesProfile"]))
				return $found["GbActivitiesProfile"];
		}
		else
		{
			$found = $this->find('list', array("conditions" => $profile));
			return $found;
		}
	}
}
