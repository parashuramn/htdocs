<?php
global $supported_versions;
$supported_versions = array("0.9", "0.95", "1.0",  "1.0.0", "1.0.1", "1.0.2", "1.0.3");
class XAPIController extends AppController {
    function beforeFilter()
    {
            parent::beforeFilter();
            global $Input, $supported_versions;
            $Output = new Output();

            $version = $Input->Version();
            if(!empty($version) && !in_array($version, $supported_versions))
                    $Output->send(400, "Error: Unsupported or invalid version [".$version."]");

            $resources = array(
                                    "about" => array("GET", "HEAD"),
                                    "statements" => array("GET", "PUT", "POST", "HEAD"),
                                    "activities" => array("GET", "PUT", "POST", "DELETE", "HEAD"),
                                    "agents" => array("GET", "PUT", "POST", "DELETE", "HEAD"),
                            );
            $res = $this->request->params["action"];
//              CakeLog::debug($res);
            if(empty($resources[$res]))
                    $Output->send(404, "");

            if(!in_array($Input->Method(), $resources[$res]))
                    $Output->send(405, "");

            if(User::get("auth") != ''  || $this->action == "about") {
                    $this->Auth->allow();
            }
            else
            {
                            $Output->send(401, "Bad access credentials or method.");
            }
    }
	 
	public function isAuthorized($user) {
		return true;
	}
	public function statements() {
		$this->layout = "ajax";
		global $Input;

		$this->loadModel("Statement");
		$this->Statement->Process($Input);
		
		$Output = new Output();
		$Output->end("STATEMENT_POST_SUCCESS");
		die();
	}
	
	function activities($request_type = null) {
		$this->layout = "ajax";
		global $Input;
		
		if(strtolower($request_type) == "state") {
		$this->loadModel("GbActivitiesState");
		$this->GbActivitiesState->Process($Input);
		}
		else if(strtolower($request_type) == "profile") {
		$this->loadModel("GbActivitiesProfile");
		$this->GbActivitiesProfile->Process($Input);
		}
		else 
		{
		$this->loadModel("Statement");
		$this->Statement->FullActivityObject($Input);
		}
	}
	
	function agents($request_type = null) {
		$this->layout = "ajax";
		global $Input;
		$Output = new Output();

		if(!(empty($request_type) || $request_type == "profile"))
			$Output->send(404, "Error: Invalid Resource");

		if(!empty($Input))
		$version = $Input->Version();
		if(empty($version))
			$Output->send(400, "Error: Empty version header");

		if(strtolower($request_type) == "profile") {
		$this->loadModel("GbAgentsProfile");
		$this->GbAgentsProfile->Process($Input);
		}
		else
		{
			$params = $Input->Params();
			$agent = json_decode(@$params["agent"]);
			$verify_agent = verify_agent($agent);
		
			if(!empty($verify_agent))
				$Output->send(400, "Error:".$verify_agent);
			
			$agent->objectType = "Person";
			$agent->name = array($agent->name);
			if(!empty($agent->mbox)) $agent->mbox = array($agent->mbox);
			if(!empty($agent->mbox_sha1sum)) $agent->mbox_sha1sum = array($agent->mbox_sha1sum);
			if(!empty($agent->openid)) $agent->openid = array($agent->openid);
			if(!empty($agent->account)) $agent->account = array($agent->account);
			
			$Output->send(200, json_encode($agent));
		}	
	}
	function about() {
		global $supported_versions;
		$this->layout = "ajax";
		$Output = new Output();
		$return = array("version" => $supported_versions);
		$Output->send(200, json_encode($return), "json");
	}
}
