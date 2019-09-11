<?php
App::uses('AppModel', 'Model');
require_once(APP."Vendor".DS."input.php");
require_once(APP."Vendor".DS."output.php");

/**
 * Statement Model
 *
 */
class Statement extends AppModel {

	public $useTable = "gb_all_statements";
/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'statement_id';
	/*public $hasMany = array("GroupAgent" => array(
						"ClassName"	=> "GroupAgent",
						"foreignKey" => "agent_id"
						));
	*//*public $hasMany = array("GroupAgent" => array(
						"ClassName"	=> "GroupAgent",
						"foreignKey" => "agent_id"
						));
	*/
	/*public $hasAndBelongsToMany = array("Group" => array(
						"className"	=> "Group",
						"joinTable"	=> "gb_group_agents",
						"foreignKey" => "agent_id",
						"associationForeignKey" => "group_id",
						"with" => "GroupAgent"
						));
*/
    public function beforeFind($queryData) {
        parent::beforeFind($queryData);
		if(!isset($queryData["recursive"]))
			$queryData["recursive"] = -1;

        if(!empty($queryData['nochange'])) {
        	return $queryData;
        }
        if(empty($queryData['conditions']))
        	$queryData['conditions'] = array();
        
        $voidedCondition = array("`Statement`.`voided` <> 1", "`Statement`.`verb_id` <> 'http://adlnet.gov/expapi/verbs/voided'");
        $queryData['conditions'] = array_merge($queryData['conditions'], $voidedCondition);

        if(check_permission("view_all_data"))
        	return modified("statements_query_data", $queryData, $this);
        
        if($view_own_data = check_permission("view_own_data")) {
	        $defaultConditions = array('Statement.authority_user_id' => User::get("id"));
	        if(empty($queryData['conditions']))
	        	$queryData['conditions'] = $defaultConditions;
	        else
	        $queryData['conditions'] = array_merge($queryData['conditions'], $defaultConditions);   
        }
        
        if($groups = check_permission("view_group_data"))
        {
        	$groups_c = implode(",", $groups);
        	if(empty($groups_c))
        		return false;

        	global $db;
	        $defaultConditions = array("Statement.agent_id IN (SELECT DISTINCT agent_id FROM ".$db->config["prefix"]."gb_group_agents WHERE group_id IN (".$groups_c."))");
	        if(empty($queryData['conditions']))
	        	$queryData['conditions'] = $defaultConditions;
	        else
	        $queryData['conditions'] = array_merge($queryData['conditions'], $defaultConditions);   
        }
        
        if(empty($view_own_data) && !count($groups))
        	return false;
        
        return modified("statements_query_data", $queryData, $this);
    }
	
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
			default:
				$Output = new Output();
				$Output->end("INVALID_REQUEST_METHOD");
		}
		exit;
	}
    public function validate_fetch_statement_params($Input) {
            global $supported_versions;

            $params = @$Input->params;
            if(empty($params))
                    return "Error: Invalid or empty parameters";

            $version = $Input->Version();
            if(empty($version))
            {
//              CakeLog::debug("empty version");
//              CakeLog::debug(print_r($Input, true));
//              CakeLog::debug(print_r($_SERVER, true));
                    return true;
            }
            if($version < "1.0" && in_array($version, $supported_versions))
            {
//                      CakeLog::debug("valid version less than 1.0");
                    return true;
            }
            if($version >= "1.0" && !in_array($version, $supported_versions))
                    return "Error: Unsupported or Invalid Version";


            $fetchPatams = array(
                            "statementId" => array( "type" => "string", "conditions" => array("alone_except" => array("format", "attachments"))),
                            "voidedStatementId" => array( "type" => "string", "conditions" => array("alone_except" => array("format", "attachments"))),
                            "agent" => array( "type" => "object" ),
                         //   "verb" => array( "type" => "IRI" ),
                         //   "activity" => array( "type" => "IRI" ),
                            "registration" => array( "type" => "UUID" ),
                            "related_activities" => array( "type" => "bool" ),
                            "related_agents" => array( "type" => "bool" ),
                    //        "since" => array( "type" => "timestamp" ),
                    //      "until" => array( "type" => "timestamp" ),
                            "limit" => array( "type" => "int" ),
                            "format" => array( "type" => "string", "conditions" => array("match" => array("ids", "exact", "canonical")) ),
                            "attachments" => array( "type" => "bool" ),
                            "ascending" => array( "type" => "bool" ),
                    );
            return gb_validate($fetchPatams, $params);
    }
	public function process_get($Input) {
		$statementId = @$Input->params["statementId"];
		//If $statementId exists return the statement else, search and return list of statements
		$Output = new Output();
		$valid = $this->validate_fetch_statement_params($Input);
		if($valid !== true)
			$Output->send(400, $valid);
		
		if(!empty($statementId)) {
			$statement = $this->find('first', array("conditions" => array("statement_id" => $statementId)));
			
			if(!empty($statement["Statement"])) {

				$statement = modified("after_get", $statement["Statement"]["statement"], "statements", $Input);
				if(!empty($Input->params["attachments"]) && ($Input->params["attachments"] == true || $Input->params["attachments"] == "true"))
				{
					$boundry = md5($statement);
					header('Content-type: multipart/mixed; boundary='.$boundry);
					$message = "\n--".$boundry."\n";
					$message.= "Content-Length:".strlen($statement)."\n";
					$message.= "Content-Type:application/json; charset=UTF-8\n\n";
					$message.= $statement;
					$message.= "\n--".$boundry."--";

					$Output->send(200, $message, "");	
				}
				else
				$Output->send(200, $statement, "json");				
			}
			else
				$Output->send(404, "Statement with ID ".$statementId." was not found.");
		}
		else
		{
			if(!empty($Input->params["continueToken"])) {
				App::import('Model', 'GbStatementContinue');
          		$GbStatementContinue = new GbStatementContinue();

          		$options = $GbStatementContinue->field("options", array('continueToken' => $Input->params["continueToken"]) );
				if(!empty($options))
				{
					$inp = unserialize($options);
				}
				if(!empty($inp))
					$Input = $inp;
				else
					$Output->send(400, "Error Bad Request, Invalid continueToken");			
			}
			
			$limit = 500;
			if(!empty($Input->params["limit"]))
			$limit = intVal($Input->params["limit"]);
			
			$conditions = array();
			if(!empty($Input->params["until"]))
				$conditions["stored <="] = $Input->params["until"];
			if(!empty($Input->params["since"]))
				$conditions["stored >"] = $Input->params["since"];
			
			if(!empty($Input->params["verb"]))
				$conditions["verb_id"] = $Input->params["verb"];
			
			if(!empty($Input->params["ID <"]))
				$conditions["ID <"] = $Input->params["ID <"];
			
			if(!empty($Input->params["ID >"]))
				$conditions["ID >"] = $Input->params["ID >"];
			
			if(!empty($Input->params["activity"])) {
				$conditions["objectid"] = $Input->params["activity"];
				//$conditions["object_objectType"] = "Activity";
			}
			$version = $Input->Version();
			if((empty($version) || $version = "0.9") && !empty($Input->params["object"])) {
				$object = gb_json_decode($Input->params["object"]);
				$conditions["objectid"] = $object->id;
				//$conditions["object_objectType"] = "Activity";
			}
			if(!empty($Input->params["registration"])) {
				$conditions["registration"] = $Input->params["registration"];
			}
			if(!empty($Input->params["actor"]) || !empty($Input->params["agent"])) {
				$agent = !empty($Input->params["agent"])? $Input->params["agent"]:$Input->params["actor"];
				$agent = gb_json_decode($agent);
                $agent_array = gb_agent_array($agent);
                if(!empty($agent_array["agent_id"])) {
                        $conditions["agent_id"] = $agent_array["agent_id"];
                }
			}
			if(!empty( $Input->params["related_activities"]) && strtolower(trim( $Input->params["related_activities"] )) != "false" && !empty( $conditions["objectid"] )) {
				$conditions[] = array('OR' => 	array(
										'objectid LIKE ' => '%'.$conditions['objectid'].'%',
										'parent_ids LIKE ' => '%'.$conditions['objectid'].'%',
										'grouping_ids LIKE ' => '%'.$conditions['objectid'].'%',
										));
				unset($conditions['objectid']);
			}
			
			$order = array("Statement.stored DESC");
			if(!empty($Input->params["ascending"])) {
				if(strtolower($Input->params["ascending"]) == "true")
					$order = array("Statement.stored");
			}

			$statements = $this->find('list', array("conditions" => $conditions, "limit"=> $limit + 1, "order" => $order, "fields" => array("statement")));
			$more = (count($statements) > $limit);
			$return["statements"] = array();
			$return["more"] = "";
			$count = 0;
			foreach($statements as $key => $statement) {
				if(empty($first_key)) $first_key = $key;
				$decoded = gb_json_decode($statement);
				if(!empty($decoded))
				$return["statements"][] = $decoded;
				$last_key = $key;
				$count++;
				if($count == $limit)
					break;
			}

			if($more)
			{
				$continueToken = generate_uuid();
				if(!empty($Input->params["ascending"]) && strtolower($Input->params["ascending"]) == "true")
					$Input->params["ID >"] = $first_key;
				else
					$Input->params["ID <"] = $last_key;
				
				$options = serialize($Input);
          		App::import('Model', 'GbStatementContinue');
          		$GbStatementContinue = new GbStatementContinue();

                //$options = str_replace("'", "''", $options); //TODO: Change to CakePHP Model
                $data = array(
                		'continueToken' => $continueToken,
                		'options'		=> $options,
                		'created'		=> date("Y-m-d H:i:s"),
                		'modified'		=> date("Y-m-d H:i:s")
                	);
                $GbStatementContinue->save($data);
				
				//Delete Old
				$conditions = array(
						'created < ' => date("Y-m-d H:i:s", time() - 2*86400) 
					);
				$GbStatementContinue->deleteAll($conditions);
				
				$return["more"] = Router::url('/', false)."xAPI/statements?continueToken=".$continueToken;
			}
			$return = modified("after_get", $return, "statements", $Input);
			$statements = json_encode($return);

			if(!empty($Input->params["attachments"]) && ($Input->params["attachments"] == true || $Input->params["attachments"] == "true"))
			{
				$boundry = md5($statements);
				header('Content-type: multipart/mixed; boundary='.$boundry);
				$message = "\n--".$boundry."\n";
				$message.= "Content-Length:".strlen($statements)."\n";
				$message.= "Content-Type:application/json; charset=UTF-8\n\n";
				$message.= $statements;
				$message.= "\n--".$boundry."--";

				$Output->send(200, $message, "");	
			}
			else
			$Output->send(200, $statements, "json");
		}
		
	}
	public function process_put($Input) {
		$params = $Input->Params();
		$Output = new Output();
		if(empty($params["statementId"])) {
			$Output->send(400, "Error Bad Request: statementId is required when using PUT method.");
		}
		$statementId = $params["statementId"];
		$options = array('conditions' => array("Statement.statement_id" => $statementId), 'recursive' => -1);
		/*echo "<pre>";
		print_r($options);
		$existing_statement = $this->find("all",$options);
		print_r($existing_statement);
		echo "</pre>";*/
		if(empty($existing_statement))
		{
			$statements = $this->prepare_statements($Input);
			if(!empty($statements))
			{
			$ids = $this->store($statements, $Input);
			header('Content-type: application/json; charset=UTF-8');
			//print_r($ids);
			$Output->send(204, json_encode($ids));
			}
			else
			$Output->send(400, "No valid statements found: ".$Input->content);
		}
		else
		$Output->send(409, "");
		//Check if statement with statementId exists. If yes, output 409 Conflict 
		//If no conflict, validate and store the statement with success/204 No Content 
	}
	public function process_post($Input) {
		//Validate and Store statements return array of statementIds
		$statements = $this->prepare_statements($Input);
		$Output = new Output();
		if(!empty($statements))
		{
			$ids = $this->store($statements, $Input);
			header('Content-type: application/json; charset=UTF-8');
			$Output->send(200, json_encode($ids));
		}
		else
		$Output->send(400, "No valid statements found: ".$Input->content);
	}
	public function prepare_statements($Input) {
		$statements = gb_json_decode($Input->content);	
		
		if($Input->Method() == "PUT") {
			$statementId = @$Input->params["statementId"];
			$statements->id = empty($statements->id)? $statementId:$statements->id;
			if(!empty($Input->params["registration"]) && empty($statements->registration))
				$statements->registration = $Input->params["registration"];
				
			return $this->prepare_statement($statements);
		}
		else if($Input->Method() == "POST")
		{
			if(is_array($statements)) {
				foreach($statements as $key => $statement) {
					if(!empty($Input->params["registration"]) && empty($statement->registration))
						$statement->registration = $Input->params["registration"];
			
					$statements[$key] = $this->prepare_statement($statement);
				}
				return $statements;
			}
			else
			return $this->prepare_statement($statements);
		}
		return null;
	}
	function prepare_statement($statement) {
		$Output = new Output();
		if(empty($statement) || !is_object($statement) || empty($statement->actor) ||  empty($statement->verb) ||  empty($statement->{"object"}))
       		$Output->send(400, "Error: Invalid or empty statement");

		$statement->id = empty($statement->id)? generate_uuid():$statement->id;
		
		if(!empty($statement->version))
		$version = $statement->version;
		else {
			if(is_string($statement->verb))
			$version = "0.9";
			else
			$version = "0.95";
		}

		$statement->actor = clean_agent($statement->actor);
		$agent = $statement->actor;	
		$verify_agent = verify_agent($agent);
		
		if(!empty($verify_agent))
			$Output->send(400, "Error:".$verify_agent);
		
		if(empty($statement->verb)) {
			$Output->send(400, "Error: Verb not found in statement: ".print_r($statment, true));
		}
		
		$statement->stored = generate_time();
		$statement->timestamp = empty($statement->timestamp)? $statement->stored:$statement->timestamp;
		$statement->timestamp = str_replace(" ", "+", $statement->timestamp);
		$statement->authority = $this->get_authority();
		return $statement;
	}

	function get_authority() {
		$authority = new stdClass;
		$authority->account = new stdClass;
		$authority->account->homePage = Router::url('/xAPI/', true);
		$auth = User::get("auth");
		$authority->account->name = $auth["auth"]["api_user"];
		$authority->objectType = "Agent";
		return $authority;
	}
	public function store($statements, $Input = null) {
			if(is_array($statements)) {
				foreach($statements as $key => $statement) {
					$return[] = $this->store_one($statement, $Input);
				}
				return $return;
			}
			else
			return array($this->store_one($statements, $Input));		
	}
	public function store_one($statement, $Input = null) {
		$options = array('conditions' => array("Statement.statement_id" => $statement->id), 'recursive' => -1);
		$existing_statement = $this->find("all",$options);
		if(!empty($existing_statement))
			return $statement->id;
		
		
		if(!empty($statement->version))
		$version = $statement->version;
		else {
			if(is_string($statement->verb))
			$version = "0.9";
			else
			$version = "0.95";
		}

		$agent = $statement->actor;

		if(is_string($statement->verb)) {
			$verb = $statement->verb;
			$verb_id = "http://adlnet.gov/expapi/verbs/" . $statement->verb;
		}
		else {
			$verb_id = $statement->verb->id;				
			$verb = @$statement->verb->display->{"en-US"};
			$verb = empty($verb)? @$statement->verb->display->{"und"}:$verb;
			$verb = empty($verb)?  substr($verb_id, strrpos($verb_id, "/") + 1):$verb;
		}
		$object_id = @$statement->{"object"}->{"id"};
		$parent_ids = implode(",", gb_parent_ids($statement));
		$grouping_ids = implode(",", gb_grouping_ids($statement));

		if(!empty($statement->authority->account->homePage) && !empty($statement->authority->account->name))
		$authority = $statement->authority->account->homePage."/".$statement->authority->account->name;
		else
		if(!empty($statement->authority->account->homePage))
		$authority = $statement->authority->account->homePage;
		else
		if(!empty($statement->authority->account->name))
		$authority = $statement->authority->account->name;
		else
		$authority = "";

		$auth = User::get("auth");
		$authority_user_id = @$auth["auth"]["user_id"];

		$object_definition_name = empty($statement->{"object"}->definition->name->{"en-US"})? (empty($statement->{"object"}->definition->name)? "": reset($statement->{"object"}->definition->name)):$statement->{"object"}->definition->name->{"en-US"};
		$object_definition_name = (!is_string($object_definition_name))? "":$object_definition_name;

		$object_definition_description = empty($statement->{"object"}->definition->description->{"en-US"})? (empty($statement->{"object"}->definition->description)? "": reset($statement->{"object"}->definition->description)):$statement->{"object"}->definition->description->{"en-US"};
		$object_definition_description = (!is_string($object_definition_description))? "":$object_definition_description;

		$statement_array = array(
			"statement_id" => $statement->id,
			'user_id' => 0,//empty($user->ID)? 0:$user->ID,
			'version' => $version,
			'verb_id' => $verb_id,
			'verb' => $verb,
			'objectid' => $object_id,
			'object_objectType' => empty($statement->{"object"}->objectType)? "":$statement->{"object"}->objectType,
			'object_definition_type' => empty($statement->{"object"}->definition->type)? "":$statement->{"object"}->definition->type,
			'object_definition_name' => $object_definition_name,
			'object_definition_description' => $object_definition_description,
			'timestamp' => empty($statement->timestamp)? "":$statement->timestamp,
			'result_score_raw' => empty($statement->result->score->raw)? "":$statement->result->score->raw,
			'result_score_scaled' => empty($statement->result->score->scaled)? "":$statement->result->score->scaled,
			'result_score_min' => empty($statement->result->score->min)? "":$statement->result->score->min,
			'result_score_max' => empty($statement->result->score->max)? "":$statement->result->score->max,
			'result_completion' => !isset($statement->result->completion)? "":((empty($statement->result->completion) || trim($statement->result->completion) == "false")? "false":"true"),
			'result_success' => !isset($statement->result->success)? "":((empty($statement->result->success) || trim($statement->result->success) == "false")? "false":"true"),
			'result_duration' => empty($statement->result->duration)? "":to_seconds($statement->result->duration),
			'parent_ids' => $parent_ids,
			'grouping_ids' => $grouping_ids,
			"stored" => empty($statement->stored)? "":$statement->stored,
			"statement" => json_encode($statement),
			"headers" => !empty($Input->headers)? serialize($Input->headers):"",
			"authority_user_id" => $authority_user_id,
			"authority" => $authority,
			"IP" => @$_SERVER["REMOTE_ADDR"],
			"voided" => false,
			"created" => date("Y-m-d H:i:s"),
			"modified" => date("Y-m-d H:i:s"),
			);
		
		$statement_array = $statement_array + gb_agent_array($agent);

		if(!empty($statement->context->registration))
			$statement_array['registration'] = $statement->context->registration;

		$statement_array = modified("before_save", $statement_array, "statements", $Input);
		$this->validate($statement_array);
	
		$this->create();
		$statment = $this->save(array("Statement" => $statement_array));
		$statement_array = $statment["Statement"];
		$statement_array = modified("after_save", $statement_array, "statements", $Input);
		include_once(APP."Vendor".DS."statement_hooks.php");
		statement_hooks($statement_array, $this);
		return $statement_array["statement_id"];
	}
	private function validate($statement) {
		$not_empty = array("agent_id", "statement_id", "objectid", "verb_id");
		foreach ($not_empty as $key) {
			if(empty($statement[$key])) {
				$Output = new Output();
				$Output->send(400, "Error: Empty ".$key);
			}
		}
	}
	public function FullActivityObject($Input) {
		$params = $Input->Params();
		$Output = new Output();
		$activityId = @$params["activityId"];
		if(empty($activityId)) {
					$Output->send(400, "Error Bad Request: activityId is required.");
		}
		
		$existing_statement = $this->find("first", array('conditions' => array("Statement.objectid" => $activityId), 'recursive' => -1));
		header('Content-type: application/json; charset=UTF-8');

		if(!empty($existing_statement["Statement"])) {
			$statement = gb_json_decode($existing_statement["Statement"]["statement"]);
			$return = json_encode(@$statement->{"object"});
			if(!empty($return))
			$Output->send(200, $return);
		}
		$Output->send(404, "");

	}
	function paginateCount($conditions = null, $recursive = 0, $extra = array()) {

		if(!empty($conditions)) 
		{
			$r = new CakeRequest();
			$e = explode("/page:", $r->url);
			$page = (int) @$e[1];
			$page = empty($page)? 1:$page;
			$return = (1 + $page * (@$extra['maxLimit']));
			return $return;
		}

		/** Original Paginate **/
		$parameters = compact('conditions');
		if ($recursive != $this->recursive) {
	            $parameters['recursive'] = $recursive;
	    }
	   	return $this->find('count', array_merge($parameters, $extra));
		/** Original Paginate **/
	}
}
