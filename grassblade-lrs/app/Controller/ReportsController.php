<?php
App::uses('AppController', 'Controller');
App::import('Controller', 'Addons');
/**
 * Statements Controller
 *
 * @property Statement $Statement
 */
class ReportsController extends AppController {

	public $uses = array("Statement");

	function beforeFilter()
	{
		parent::beforeFilter();
//		$this->Auth->allow("bell_chart");

		$allowed = modified("reports_auth_allow", array(), $this);
		if(is_array($allowed)) {
			foreach ($allowed as $allow) {	
				if(in_array($allow, array('bell_chart', 'score_avg', 'extra')))
				$this->Auth->allow($allow);
			}
		}

		if(!empty($_GET["filter_name"]) && !empty($_GET["save_filter"]))
		{
			$this->saveReportFilter($_GET);
		}
		if(!empty($_GET["filter_id"]) && !empty($_GET["delete_filter"]))
		{
			$this->deleteReportFilter($_GET);
		}
		if(!empty($_GET["save_filter_email"]))
		{
			$this->saveReportFilterEmail($_GET);
		}
		if(!empty($_GET["delete_filter_email"]) && !empty($_GET["filter_id"]))
		{
			$this->deleteReportFilterEmail($_GET);
		}
         // delete old csv files           
        /*$files = glob(TMP.'csv\*'); // get all file names
        if(!empty($files)) {
            foreach($files as $file){ // iterate files   
                if(filemtime($file) < time()) {
                    if(is_file($file))
                        unlink($file);
                }
            }
        } */   

        if( in_array($this->request->params["action"], array("index")) ) {
        	modified("fetch_contents_data", "", $this);
        }
	}
    function extra($report_name) {
        modified("reports_extra", '', $this, $report_name);        
    }
	function saveReportFilter($get) {
	//	echo "<pre>";
	//	print_r($get);
        $filter = array();
		if(empty($get["filter_id"])) 
		{
			$filters = grassblade_config_get("filters");
			if(empty($filters)) 
			{
				$filters = array();
				$id = 1;
			}
			else
			{
				$id = max($filters) + 1;
			}
			$filters[$id] = $id;
			grassblade_config_set("filters", $filters);
            $filter["user"] = User::get("id");
		}
		else {
			$id = $get["filter_id"];
            $filter =  grassblade_config_get("filter_".$id);
        }
		$_GET["filter_id"] = $get["filter_id"] = $id;
		unset($get["save_filter"]);
		$filter["GET"] = $get;
		$filter["action"] = $this->request->params["action"];
		$filter["controller"] = $this->request->params["controller"];
		grassblade_config_set("filter_".$id, $filter);

		$filter_url = gb_get_filter_url($id);
		$this->redirect($filter_url);
		exit;
	//	print_r($filter);
	//	exit;
	}
	function saveReportFilterEmail($data) {
		$filter_id = intVal($data["save_filter_email"]);
		if(empty($filter_id) || empty($data["filter_email_email_ids_".$filter_id]))
		{
			$this->redirect("/");
			exit;
		}

		$filter_emails = grassblade_config_get("filter_emails_".$filter_id);
                if(empty($filter_emails) || !is_array($filter_emails))
                $filter_emails = array();

		$save_data = array();
		$id = md5(serialize($data));
		$save_data["id"] = $id;
		$save_data["filter_id"] = $filter_id;
		$save_data["email_ids"] = $data["filter_email_email_ids_".$filter_id];
		$save_data["type"] = $data["filter_email_type_".$filter_id];
        $save_data["days"] = $data["filter_email_no_of_days_".$filter_id];
		$save_data["freq"] = $data["filter_email_freq_".$filter_id];
		$save_data["freq_text"] = $data["filter_email_freq_text_".$filter_id];
		$save_data["column_values"] = $data["filter_email_column_values_".$filter_id];
		$save_data["sort"] = $data["filter_email_sort_".$filter_id];
		$save_data["sort_direction"] = $data["filter_email_sort_direction_".$filter_id];
		$filter_emails[$id] = $save_data;
		grassblade_config_set("filter_emails_".$filter_id, $filter_emails);

		$filter_url = gb_get_filter_url($filter_id);
		$this->redirect($filter_url);
		exit;
	}
	function deleteReportFilterEmail($data) {
		if(empty($data["delete_filter_email"]) || empty($data["filter_id"]))
			return;

		$filter_id = $data["filter_id"];
		$filter_emails = grassblade_config_get("filter_emails_".$filter_id);
		unset($filter_emails[$data["delete_filter_email"]]);
		grassblade_config_set("filter_emails_".$filter_id, $filter_emails);
		$filter_url = gb_get_filter_url($filter_id);
		$this->redirect($filter_url);
		exit;
	}
	function deleteReportFilter($get) {
		if(empty($get["filter_id"])) 
			return;

		$id = $get["filter_id"];
		$filters = grassblade_config_get("filters");
		$filter = grassblade_config_get("filter_".$id);

		$report_url = Router::url(array("controller" => $filter["controller"], "action" => $filter["action"]), true);
		unset($filters[$id]);
		grassblade_config_set("filters", $filters);
		
		grassblade_config_delete("filter_".$id);
		grassblade_config_delete("filter_emails_".$id);
		$this->redirect($report_url);
		exit;
	}
	function beforeRender() {
		global $gbdb;
		$this->set("gbdb", $gbdb);
		$this->set("verbs_report_url", Router::url(array('controller' => 'Reports', 'action' => 'verbs',  "sort" => "no_of_statements", "direction" => "desc")));
		$this->set("activity_stream_report_url", Router::url(array('controller' => 'Reports', 'action' => 'index')));
		$this->set("activity_stream_report_url_timestamp_asc", Router::url(array('controller' => 'Reports', 'action' => 'index', "sort" => "timestamp", "direction" => "asc")));
		$this->set("attempts_report_url", Router::url(array('controller' => 'Reports', 'action' => 'attempts')));
		$this->set("attempts_summary_report_url", Router::url(array('controller' => 'Reports', 'action' => 'attempts_summary')));
		$this->set("retrigger_url", Router::url(array('controller' => 'Reports', 'action' => 'retrigger')));
		$this->set("agents_report_url", Router::url(array('controller' => 'Reports', 'action' => 'agents',  "sort" => "no_of_statements", "direction" => "desc")));
		$this->set("activities_report_url", Router::url(array('controller' => 'Reports', 'action' => 'activities',  "sort" => "no_of_statements", "direction" => "desc")));
		App::import('Model', 'Group');
		$GroupModel = new Group();
        $groups_list = $GroupModel->find("all");    
		$groups_list_to_show = array();
        if(!empty($groups_list))
        foreach ($groups_list as $key => $group) {
            if(User::get("role") != "admin")
            {
                $group_leaders = unserialize($group["Group"]["group_leaders"]);
                if(!in_array(User::get("email"), $group_leaders) && !in_array(User::get("id"), $group_leaders)) {
                    unset($groups_list[$key]);
                    continue;
                }
            }
	    $groups_list_to_show[$group["Group"]["id"]] = $group["Group"]["name"];
        }
        $this->set("groups_list", $groups_list_to_show);
		$this->set('content_types', get_content_types());
		$this->set('contents_list', get_categorized_content_list());
    }
	 public function isAuthorized($user) {
		return true;
	}
	function retrigger_all() {
	/*	include_once(APP."Vendor/statement_hooks.php");
		$conditions = array();
		$conditions['verb_id'] = array();
		$conditions['verb_id'][] = "http://adlnet.gov/expapi/verbs/completed";
		$conditions['verb_id'][] = "http://adlnet.gov/expapi/verbs/passed";
		//$conditions['verb_id'][] = "http://adlnet.gov/expapi/verbs/failed";
		$statements = $this->Statement->find("all", array("conditions" => $conditions, "limit" => 200));
		echo count($statements);
		echo "<br><pre>";
		$count = 0;
		foreach ($statements as $key => $value) {
			$count++;
			$message = statement_hooks($value["Statement"], $this->Statement);
			echo $count.":<br>";
			print_r( $message);
			echo "<br><br>";
			flush();
		}*/
		exit;
	}
	function retrigger($id) {
		$this->autoRender = false;
		$this->request->onlyAllow('ajax');

		if(empty($id)) {
			$return = array(
					"success"	=> 0,
					"message"	=> "ID is required"
				);
			return json_encode($return);
		}

		$conditions = array("id"	=> $id);
		$statement = $this->Statement->find("first", array("conditions" => $conditions));
		$statement = @$statement["Statement"];
		if(empty($statement)) {
			$return = array(
					"success"	=> 0,
					"message"	=> "Statement not found."
				);
			return json_encode($return);			
		}
		else
		{
			include_once(APP."Vendor".DS."statement_hooks.php");
			$message = statement_hooks($statement, $this->Statement);
			$return = array(
					"success"	=> 1,
					"message"	=> $message
				);
			return json_encode($return);
		}
	}
	function bell_chart($score_type = "first") {
		$this->layout = "ajax";
		if(empty($_REQUEST["objectid"]))
			exit;
        $objectid = $_REQUEST["objectid"];
        $bell_conditions = array(
                                    "objectid" => $objectid,
                                    "OR" => array(
                                            "result_score_scaled >= 0",
                                            "result_score_raw >= 0"
                                    )
                            );
        if(!empty($_REQUEST["agent_id"]))
        {
                if(is_array($_REQUEST["agent_id"]))
                $users = $_REQUEST["agent_id"];
                else
                $users = array($_REQUEST["agent_id"]);
        }

        //check score_list cache
        $scores_list_cache = gb_get_db_cache("bell_chart_score_list_".$objectid);

        //if exists, check if needs to refresh
        if(!empty($scores_list_cache["value"])) {
            if($scores_list_cache["timestamp"] > time() - 5 * 60) {
                $scores_list = $scores_list_cache["value"];
                $cache_used = true;
            }    
            else
            {
                $latest_statement = $this->Statement->find("first", array("fields" => array("Statement.timestamp"), "conditions" => $bell_conditions, "order" => "Statement.timestamp DESC"));
                if(!empty($latest_statement["Statement"]["timestamp"])) {
                    $latest_statement_timestamp = $latest_statement["Statement"]["timestamp"];
                    if($latest_statement_timestamp < $scores_list_cache["timestamp"])
                    {
                        $scores_list = $scores_list_cache["value"];
                        $cache_used = true;
                    }
                }
            }
        }
        
        //skip fetch if has list from cache
        if(!isset($scores_list)) {
            $this->Statement->recursive = 0;
            $scores_list = $this->Statement->find("all", array("fields" => array("Statement.agent_id", "Statement.result_score_scaled", "Statement.result_score_raw", "timestamp"), "conditions" => $bell_conditions, "order" => "agent_id, timestamp ASC"));
        
            gb_set_db_cache("bell_chart_score_list_".$objectid, $scores_list, time() + 30 * 86400);
        }

        if(empty($scores_list))
            exit;

        $scores = array();
        foreach ($scores_list as $score_array) {
            $agent_id = $score_array["Statement"]["agent_id"];
            $score = !is_null($score_array["Statement"]["result_score_scaled"])? $score_array["Statement"]["result_score_scaled"]*100:$score_array["Statement"]["result_score_raw"];
            $score = floatval($score);

            switch ($score_type) {
                case 'max':
                    if(empty($scores[$agent_id]) || $score > $scores[$agent_id])
                    $scores[$agent_id] = $score;
                
                    break;
                case 'min':
                    if(empty($scores[$agent_id]) || $score < $scores[$agent_id])
                    $scores[$agent_id] = $score;
                    break;
                case 'avg':
                    $scores[$agent_id][] = $score;
                    break;

                case 'first':
                default:
                    if(empty($scores[$agent_id]))
                    $scores[$agent_id] = $score;
                    break;
            }

            if(!empty($users) && in_array($agent_id, $users))
            {

                $user_scores[$agent_id] = $score;
            }
        }

        if(empty($scores))
            exit;
        
        if($score_type == "avg")
        {
            foreach ($scores as $agent_id => $agent_scores) {
                $scores[$agent_id] = array_sum($agent_scores)/count($agent_scores);
            }
        }

        if(count($scores) < 2)
            exit; //Bell chart needs scores from atleast two users

        $variance = false;
        $last = null;

        //Check Variance
        foreach ($scores as $agent_id => $score) {
            if(!is_null($last) && $last != $score) {
                $variance = true;
                break;
            }

            if(is_null($last))
                $last = $score;
        }

        if(!$variance)
            exit; //Bell chart needs atleast two different scores
        
        $return = array();
        $return["scores"] = array_values($scores);
        if(!empty($users))
            $return["user_scores"] = $user_scores;
        else
            $return["user_scores"] = "";
        
        $return["cache_used"] = @$cache_used;

        echo json_encode($return);
        exit;
    }
    function score_avg() {
    	$objectids = $_REQUEST['objectid'];
    	$objectids = !is_array($objectids)? array($objectids):$objectids;
    	$return = array();
    	foreach ($objectids as $objectid) {
			if(filter_var($objectid, FILTER_VALIDATE_URL)) {
				if(empty($_GET['nocache']))
				$existing_data = grassblade_config_get("score_avg_".$objectid);

				if(!empty($existing_data))
				{
					$return[$objectid] = $existing_data;
					continue;
				}

				$fields = array(
						'max(id) as max_id',
						'min(result_score_raw) as raw_min',
						'max(result_score_raw) as raw_max',
						'avg(result_score_raw) as raw_avg',
						'min(result_score_scaled) as scaled_min',
						'max(result_score_scaled) as scaled_max',
						'avg(result_score_scaled) as scaled_avg',
						'count(id) as count'	
					);
				$conditions = array(
								'objectid' => $objectid,
								'verb_id' => array(
											"http://adlnet.gov/expapi/verbs/passed",
											"http://adlnet.gov/expapi/verbs/failed"
										)
							);
				$results = $this->Statement->find("all", array('fields' => $fields, 'conditions' => $conditions, "nochange" => true));
				if(!empty($results[0][0])) {
					$return[$objectid] = $results[0][0];
					grassblade_config_set("score_avg_".$objectid, $results[0][0]);
				}
			}
    	}
    	if(empty($_GET['show'])) {
    		echo json_encode($return);
    		exit();
    	}
    }
	function count($what = "statements", $start = "", $end = "") {
	//	echo date("Y-m-d", ($start)).":".date("Y-m-d", ($end)).":";
		$user_id = User::get("id");
		$key = "Reports.count.".$what.$user_id;
		$stored_counts = grassblade_config_get($key, array());
		///echo "<pre>";print_r($stored_counts);

		$conditions = $this->get_conditions();
		//$statementModel = new Statement();
		if(!empty($end)) {
			$now = strtotime($end);
			$conditions['DATE(timestamp) <='] = date("Y-m-d", $now);
		}
		else
		if(!empty($conditions['timestamp <=']))
		$now = strtotime($conditions['timestamp <=']);
		else
		$now = time();

		if(!empty($start)) 
			$start_timestamp = strtotime($start);
		else
			$start_timestamp = strtotime("-29 day", $now);

		$start_date = date("Y-m-d", $start_timestamp);
		$conditions["DATE(Statement.timestamp) >="] = $start_date;

	//	echo $start_date.":".date("Y-m-d", $now);

		if(!empty($conditions['timestamp >='])) {
			$conditions_start_timestamp = strtotime($conditions['timestamp >=']);
			if($conditions_start_timestamp <= $start_timestamp ) {
				$conditions['DATE(Statement.timestamp) >='] = $start_date;
				unset($conditions['timestamp >=']);
			}
			else
			{
				$start_timestamp = strtotime($conditions['timestamp >=']);
				$start_date = date("Y-m-d", $start_timestamp);
			}
		}//print_r($conditions);

		$current_timestamp = $start_timestamp;
		$counts_array = array();
		$last_max_id = 0;
		while($current_timestamp <= $now ) {
			$current_date = date("Y-m-d", $current_timestamp);
			$current_timestamp = strtotime($current_date);

			if(isset($stored_counts[$current_date])) {
				$value = $stored_counts[$current_date]["Count"];
				$counts_array[] = array($current_timestamp*1000, $value);
				$current_timestamp =  strtotime("+1 day", $current_timestamp);
				if(isset($stored_counts[$current_date]["max_id"]) && $stored_counts[$current_date]["max_id"] > $last_max_id)
				$last_max_id 	= $stored_counts[$current_date]["max_id"];		
			}
			else {
				$start_timestamp = $current_timestamp;
				$start_date = date("Y-m-d", $start_timestamp);
				$conditions['DATE(Statement.timestamp) >='] = $start_date;
				if(!empty($last_max_id))
					$conditions['Statement.id >'] = $last_max_id;

				break;
			}
		}

		switch ($what) {
            case 'statements':
                $counts_results = $this->Statement->find('all', array("fields" => "COUNT(DISTINCT `Statement`.`id`) as Count, DATE(Statement.timestamp) as Date, max(`Statement`.`id`) as max_id, min(`Statement`.`id`) as min_id", "group" => "DATE(Statement.timestamp)", "conditions" => $conditions));
                break;
            case 'verbs':
                $counts_results = $this->Statement->find('all', array("fields" => "COUNT(DISTINCT `Statement`.`verb_id`) as Count, DATE(Statement.timestamp) as Date, max(`Statement`.`id`) as max_id, min(`Statement`.`id`) as min_id", "group" => "DATE(Statement.timestamp)", "conditions" => $conditions));
                break;
            default:
                $counts_results = $this->Statement->find('all', array("fields" => "COUNT(DISTINCT `Statement`.`id`) as Count, DATE(Statement.timestamp) as Date, max(`Statement`.`id`) as max_id, min(`Statement`.`id`) as min_id", "group" => "DATE(Statement.timestamp)", "conditions" => $conditions));
                break;
        }
		$counts = array();
		if(!empty($counts_results))
		foreach ($counts_results as $count) {
			$counts[$count[0]["Date"]] = $count[0];
		}

		/*//Verbs
		$counts = $this->Statement->find('all', array("fields" => "COUNT(DISTINCT `Statement`.`verb_id`) as Count, DATE(Statement.timestamp) as Date", "group" => "DATE(Statement.timestamp)", "conditions" => $conditions));
		$verb_counts = array();
		foreach ($counts as $count) {
			$verb_counts[$count[0]["Date"]] = $count[0]["Count"];
		}
		*/

		$current_timestamp = $start_timestamp;
		while($current_timestamp <= $now ) {
			$current_date = date("Y-m-d", $current_timestamp);
			$current_timestamp = strtotime($current_date);
			$value = empty($counts[$current_date]["Count"])? 0:$counts[$current_date]["Count"];
			$counts_array[] = array($current_timestamp*1000, $value);
			//$value = empty($verb_counts[$current_date])? 0:$verb_counts[$current_date];
			//$verbs[] = array($current_timestamp*1000, $value);
			$current_timestamp =  strtotime("+1 day", $current_timestamp);

			if($current_date != date("Y-m-d")) {
				if(isset($counts[$current_date]))
				$stored_counts[$current_date] = $counts[$current_date];
				else
				$stored_counts[$current_date] = array(
													"Count" => 0,
													"Date"	=> $current_date
											);
			}
		}
		grassblade_config_set($key, $stored_counts);
		echo  json_encode($counts_array);
		exit;
	}
	function dashboard($type = null) {
		$this->set("page_title", __("Dashboard"));

        if(!empty($type)) {
            $this->layout = "ajax";
            $this->render("../Elements/dashboard-".$type);
	    }
	    else
	    {
	        if(!function_exists('curl_version'))
	        {
				$this->Session->setFlash(__('cURL not installed/enabled. Please make sure cURL is installed and enabled for PHP.'), 'default', array('class' => 'note note-warning'));
	        }
	    }
    }
	function verbs($request = null) {
		$this->set("page_title", __("Verbs"));

		if(empty($request) || !is_array($request))
			$request = $this->request_data($_REQUEST);

		$limit = 10000;
		/*
		if(!empty($request["layout"]) && $request["layout"] == "ajax")
			$limit = 200;
		else
			$limit = 50;
		*/
		$limit = !empty($request["limit"])? $request["limit"]:$limit;

		if (!empty($request['column_values'])) { // for pdf download
            $limit = 1000000;
            $selColumns = explode(",", $request['column_values']);
        }
        else
        	$selColumns = array();

		if(!empty($request["csv"]))
		$limit = 1000000;
		$conditions = $this->get_conditions($request);

		$no_filtering = $use_caching = empty($conditions) || (is_array($conditions) && count($conditions) < 1);

		if($no_filtering) 
		{
			$last_statement_id = $this->Statement->find("first", array("order" => "ID DESC") );
			$last_statement_id = (int) @$last_statement_id["Statement"]["id"];
			if($last_statement_id < 100000)
				$use_caching = false;			
		}

		if($use_caching) {
			$conditions["ID <= "] = $last_statement_id;

			$user_id = User::get("id");
			$config_key = "verbs.".$user_id;
			$stored_statements = grassblade_config_get($config_key, array());

			$config_key2 = "verbs.last_statement_id.".$user_id;
			$stored_last_statement_id = grassblade_config_get($config_key2, 0);
			$conditions["ID > "] = $stored_last_statement_id;
		}

		$this->set("conditions", $conditions);
		$this->Statement->recursive = 0;
		$this->Statement->virtualFields['no_of_statements'] = 'count(verb_id)';
		$paginate = array("fields" => array("verb_id", "verb", "no_of_statements"), "conditions" => $conditions, "group" => "verb_id", "limit" => $limit, 'maxLimit' => 10000);

		if(!empty($request["sort"]))
			$paginate["sort"] = $request["sort"];
		if(!empty($request["sort_direction"]))
			$paginate["direction"] = $request["sort_direction"];

		$this->paginate = $paginate;
        //$this->Paginator->settings = $this->paginate;
		$gbStatements = $this->paginate();

		if(!empty($gbStatements))
		foreach ($gbStatements as $key => $gbStatement) {
			if(!empty($gbStatements[$key][0]["Statement__no_of_statements"]))
				$gbStatements[$key]["Statement"]["no_of_statements"] = $gbStatements[$key][0]["Statement__no_of_statements"];
			/*
			[Statement] => Array
                (
                    [verb_id] => http://adlnet.gov/expapi/verbs/interacted
                    [verb] => interacted
                    [no_of_statements] => 71162
                )
                */
			$verb_id = $gbStatement["Statement"]["verb_id"];
			if(!empty($stored_statements[$verb_id])) {
				$gbStatements[$key]["Statement"]["no_of_statements"] += (int) $stored_statements[$verb_id]["no_of_statements"];
				unset($stored_statements[$verb_id]);
			}
		}
		if($use_caching) {
			if(!empty($stored_statements))
			foreach ($stored_statements as $key => $value) {
				$gbStatements[] = array("Statement" => $value);
			}

			$stored_statements = array();
			foreach ($gbStatements as $key => $value) {
				$stored_statements[$value["Statement"]["verb_id"]] = $value["Statement"];
			}
			grassblade_config_set($config_key, $stored_statements);
			grassblade_config_set($config_key2, $last_statement_id);
		}

		if(!empty($gbStatements))
		foreach ($gbStatements as $key => $gbStatement) {
			if(!empty($gbStatements[$key][0]["Statement__no_of_statements"]))
				$gbStatements[$key]["Statement"]["no_of_statements"] = $gbStatements[$key][0]["Statement__no_of_statements"];
			$gbStatements[$key]["Statement"] = $this->report_format($gbStatements[$key]["Statement"], $request);
		}
		if(!empty($request["csv"])) {
			$gbStatements = $this->prepare_export($gbStatements, "verbs", $selColumns);
			$file = $this->batchcsv($gbStatements, time(), 0, "verbs");
            if(@$request["return_type"] == "path" || @$request["return_type"] == "url")
				return $file;
			else
				$this->out($file);
		}
		if (!empty($request["pdf"])) {
			if($use_caching) {
				unset($conditions["ID <= "]);
				unset($conditions["ID > "]);
			}
            $statement = $this->prepare_pdf_export($gbStatements, "verbs",$selColumns);
            $addons = new AddonsController;
            $export_pdf['header_image'] = 'header.png';
             // pdf header image
            $export_pdf['footer_image'] = 'footer.png';
             // pdf footer image
            $export_pdf['table_headers'] = $statement['headers'];
             // pdf table header
            $export_pdf['table_data'] = $statement['columns'];
             // pdf table data
            $export_pdf['report_type'] = $this->viewVars["page_title"];
             // pdf file name
            $export_pdf['return_type'] = empty($request["return_type"])? 'download':$request["return_type"];
             //path or url or download
             $export_pdf['filtered_by'] = $conditions;

            $getPdf = $addons->generatePdf($export_pdf);
            if(@$request["return_type"] == "path" || @$request["return_type"] == "url")
            	return $getPdf;
        }
		$this->set('gbStatements', $gbStatements);
		if(!empty($request["layout"]) && $request["layout"] == "ajax") {
			$verbs = array();
			foreach ($gbStatements as $gbStatement) {
				$verbs[$gbStatement["Statement"]["verb_id"]] = $gbStatement["Statement"]; 
			}
			echo json_encode($verbs);
			exit;
		}
		if(!empty($request["layout"]) && $request["layout"] == "ajax_html") {
			$this->layout = "ajax";
		}
		
		$this->set("verbs_report_url", Router::url(array('controller' => 'Reports', 'action' => 'verbs',  "sort" => "no_of_statements", "direction" => "desc")));

	}
	function agents($request = null) {
		$this->set("page_title", __("Learners"));

		if(empty($request) || !is_array($request))
			$request = $this->request_data($_REQUEST);

		if(!empty($request["layout"]) && $request["layout"] == "ajax")
			$limit = 200;
		else
			$limit = 50;
		$limit = !empty($request["limit"])? $request["limit"]:$limit;
		if (!empty($request['column_values'])) { // for pdf
            $limit = 1000000;
            $selColumns = explode(",", $request['column_values']);
        }
        else
    	$selColumns = array();

		if(!empty($request["csv"]))
		$limit = 1000000;
		$conditions = $this->get_conditions($request);

		$this->set("conditions", $conditions);
		$this->Statement->recursive = 0;
		$this->Statement->virtualFields['no_of_statements'] = 'count(agent_id)';
		$paginate = array("fields" => array("agent_id", "agent_mbox", "agent_name", "no_of_statements"), "conditions" => $conditions, "group" => "agent_id", "limit" => $limit, 'maxLimit' => 10000);

		if(!empty($request["sort"]))
			$paginate["sort"] = $request["sort"];
		if(!empty($request["sort_direction"]))
			$paginate["direction"] = $request["sort_direction"];

		$this->paginate = $paginate;
        //$this->Paginator->settings = $this->paginate;
		$gbStatements = $this->paginate();
		foreach ($gbStatements as $key => $gbStatement) {
			if(!empty($gbStatements[$key][0]["Statement__no_of_statements"]))
				$gbStatements[$key]["Statement"]["no_of_statements"] = $gbStatements[$key][0]["Statement__no_of_statements"];
			$gbStatements[$key]["Statement"] = $this->report_format($gbStatements[$key]["Statement"], $request);
		}
		if(!empty($request["csv"])) {
			$gbStatements = $this->prepare_export($gbStatements, "agents", $selColumns);
			$file = $this->batchcsv($gbStatements, time(), 0, "agents");
            if(@$request["return_type"] == "path" || @$request["return_type"] == "url")
				return $file;
			else
				$this->out($file);
		}
		 if (!empty($request["pdf"])) {
            $statement = $this->prepare_pdf_export($gbStatements, "agents",$selColumns);
           	$addons = new AddonsController;
            $export_pdf['header_image'] = 'header.png';  // pdf header image
            $export_pdf['footer_image'] = 'footer.png';  // pdf footer image
            $export_pdf['table_headers'] = $statement['headers']; // pdf table header
            $export_pdf['table_data'] = $statement['columns']; // pdf table data
            $export_pdf['report_type'] =  $this->viewVars["page_title"];  // pdf file name
            $export_pdf['return_type'] = empty($request["return_type"])? 'download':$request["return_type"];  //path or url or download
            $export_pdf['filtered_by'] = $conditions;
            $getPdf = $addons->generatePdf($export_pdf);
            if(@$request["return_type"] == "path" || @$request["return_type"] == "url")
            	return $getPdf;
         }
		$this->set('gbStatements', $gbStatements);
		if(!empty($request["layout"]) && $request["layout"] == "ajax") {
			$agents = array();
			if(!empty($gbStatements))
			foreach ($gbStatements as $gbStatement) {
				$agents[$gbStatement["Statement"]["agent_id"]] = $gbStatement["Statement"]; 
			}
			echo json_encode($agents);
			exit;
		}
	}
    function timespent($request = null) {
        $this->set("page_title", __("Time Spent"));

        if(empty($request) || !is_array($request))
            $request = $this->request_data($_REQUEST);

        if(!empty($request["layout"]) && $request["layout"] == "ajax")
            $limit = 200;
        else
            $limit = 50;
        $limit = !empty($request["limit"])? $request["limit"]:$limit;
        if (!empty($request['column_values'])) { // for pdf
            $limit = 1000000;
            $selColumns = explode(",", $request['column_values']);
        }
        else
        $selColumns = array();

        if(!empty($request["csv"]))
        $limit = 1000000;
        $conditions = $this->get_conditions($request);

        $this->set("conditions", $conditions);
        $this->Statement->recursive = 0;
        $this->Statement->virtualFields['timespent'] = 'sum(result_duration)';
        $paginate = array("fields" => array("agent_id", "agent_mbox", "agent_name", "timespent"), "conditions" => $conditions, "group" => "agent_id HAVING sum(result_duration) > 0", "limit" => $limit, 'maxLimit' => 10000);

        if(!empty($request["sort"]))
            $paginate["sort"] = $request["sort"];
        if(!empty($request["sort_direction"]))
            $paginate["direction"] = $request["sort_direction"];

        $this->paginate = $paginate;
        //$this->Paginator->settings = $this->paginate;
        $gbStatements = $this->paginate();
        //echo "<pre>";print_r($gbStatements);exit;

        foreach ($gbStatements as $key => $gbStatement) {
            $gbStatements[$key]["Statement"]["timespent"] = isset($gbStatements[$key]["Statement"]["timespent"])? $gbStatements[$key]["Statement"]["timespent"]:$gbStatements[$key][0]["Statement__timespent"];
            $gbStatements[$key]["Statement"]["readable_timespent"] = to_time($gbStatements[$key]["Statement"]["timespent"]);
            $gbStatements[$key]["Statement"] = $this->report_format($gbStatements[$key]["Statement"], $request);
        }
        if(!empty($request["csv"])) {
            $gbStatements = $this->prepare_export($gbStatements, "timespent", $selColumns);
            $file = $this->batchcsv($gbStatements, time(), 0, "agents");
            if(@$request["return_type"] == "path" || @$request["return_type"] == "url")
                return $file;
            else
                $this->out($file);
        }
         if (!empty($request["pdf"])) {
            $statement = $this->prepare_pdf_export($gbStatements, "timespent",$selColumns);
            $addons = new AddonsController;
            $export_pdf['header_image'] = 'header.png';  // pdf header image
            $export_pdf['footer_image'] = 'footer.png';  // pdf footer image
            $export_pdf['table_headers'] = $statement['headers']; // pdf table header
            $export_pdf['table_data'] = $statement['columns']; // pdf table data
            $export_pdf['report_type'] =  $this->viewVars["page_title"];  // pdf file name
            $export_pdf['return_type'] = empty($request["return_type"])? 'download':$request["return_type"];  //path or url or download
            $export_pdf['filtered_by'] = $this->pdf_filters($conditions, $request);
            $getPdf = $addons->generatePdf($export_pdf);
            if(@$request["return_type"] == "path" || @$request["return_type"] == "url")
                return $getPdf;
         }
        $this->set('gbStatements', $gbStatements);
        if(!empty($request["layout"]) && $request["layout"] == "ajax") {
            $agents = array();
            if(!empty($gbStatements))
            foreach ($gbStatements as $gbStatement) {
                $agents[$gbStatement["Statement"]["agent_id"]] = $gbStatement["Statement"]; 
            }
            echo json_encode($agents);
            exit;
        }
    }
    function pdf_filters($conditions, $request) {
        unset($conditions[0]);
        if(!empty($request["group"])) {
            if(!class_exists("Group"))
            App::import('Model', 'Group');

            $GroupModel = new Group();
            $group_ids =  !is_array($request["group"])? array($request["group"]):$request["group"];
            $groups = array();
            foreach ($group_ids as $group_id) {
                $options = array('conditions' => array('Group.' . $GroupModel->primaryKey => $group_id), 'fields' => array("id","name"));
                $g = $GroupModel->find('first', $options);
                
                if(!empty($g["Group"]["name"]))
                    $groups[$g["Group"]["id"]] = $g["Group"]["name"];
            }
            $conditions["Groups"] = $groups;    
        }
        return $conditions;
    }
	function activities($request = null) {
		$this->set("page_title", __("Activities"));

		if(empty($request) || !is_array($request))
			$request = $this->request_data($_REQUEST);

		if(!empty($request["layout"]) && $request["layout"] == "ajax")
			$limit = 200;
		else
			$limit = 50;
		$limit = !empty($request["limit"])? $request["limit"]:$limit;
		 if (!empty($request['column_values'])) {
            $limit = 1000000;
            $selColumns = explode(",", $request['column_values']);
        }
        else
    	$selColumns = array();

		if(!empty($request["csv"]))
		$limit = 1000000;
		$conditions = $this->get_conditions($request);

		$this->set("conditions", $conditions);
		$this->Statement->recursive = 0;
		$this->Statement->virtualFields['no_of_statements'] = 'count(objectid)';
		$paginate = array("fields" => array("no_of_statements", "objectid", "object_definition_name", "object_definition_type", "object_definition_description", "parent_ids", "grouping_ids"), "conditions" => $conditions, "group" => "objectid", "limit" => $limit, 'maxLimit' => 10000);

		if(!empty($request["sort"]))
			$paginate["sort"] = $request["sort"];
		if(!empty($request["sort_direction"]))
			$paginate["direction"] = $request["sort_direction"];

		$this->paginate = $paginate;
        //$this->Paginator->settings = $this->paginate;
		$gbStatements = $this->paginate();
		if(!empty($gbStatements))
		foreach ($gbStatements as $key => $gbStatement) {
			if(!empty($gbStatements[$key][0]["Statement__no_of_statements"]))
				$gbStatements[$key]["Statement"]["no_of_statements"] = $gbStatements[$key][0]["Statement__no_of_statements"];
			$gbStatements[$key]["Statement"] = $this->report_format($gbStatements[$key]["Statement"], $request);
		}
		if(!empty($request["csv"])) {
			$gbStatements = $this->prepare_export($gbStatements, "activities", $selColumns);
			$file = $this->batchcsv($gbStatements, time(), 0, "activities");
            if(@$request["return_type"] == "path" || @$request["return_type"] == "url")
				return $file;
			else
				$this->out($file);
		}
		 if (!empty($request["pdf"])) {
            $statement = $this->prepare_pdf_export($gbStatements, "activities",$selColumns);
            $addons = new AddonsController;
            $export_pdf['header_image'] = 'header.png'; // pdf header image
            $export_pdf['footer_image'] = 'footer.png'; // pdf footer image
            $export_pdf['table_headers'] = $statement['headers']; // pdf table header
            $export_pdf['table_data'] = $statement['columns']; // pdf table data
            $export_pdf['report_type'] =  $this->viewVars["page_title"]; // pdf file name
            $export_pdf['return_type'] = empty($request["return_type"])? 'download':$request["return_type"];  //path or url or download
            $export_pdf['filtered_by'] = $conditions;
            $getPdf = $addons->generatePdf($export_pdf);
            if(@$request["return_type"] == "path" || @$request["return_type"] == "url")
            	return $getPdf;
         }
		$this->set('gbStatements', $gbStatements);
		
		if(!empty($request["layout"]) && $request["layout"] == "ajax") {
			$activities = array();
			foreach ($gbStatements as $gbStatement) {
				$activities[$gbStatement["Statement"]["objectid"]] = $gbStatement["Statement"]; 
			}
			echo json_encode($activities);
			exit;
		}
	}
	public function index($request = null) {
		$this->set("page_title", __("Activity Stream"));

		if(empty($request) || !is_array($request))
			$request = $this->request_data($_REQUEST);

		$conditions = $this->get_conditions($request);
		$limit = !empty($request["limit"])? $request["limit"]:50;
        
        if (!empty($request['column_values'])) {
            $selColumns = explode(",", $request['column_values']);
        }
        else
    	$selColumns = array();

        if (!empty($request['pdf'])) { // for pdf
                $limit = 100000;
        }

		if (!empty($request["csv"])) {    // for csv
			set_time_limit(0);
            $limit = 100;
            $count = 100;
            $time = time();
			$this->Statement->cacheQueries = false;
            for ($offset = 0; $offset <= $count; $offset += $limit) {
                $this->set("conditions", $conditions);
                $this->Statement->recursive = 0;
                $paginate = array("conditions" => $conditions, "order" => "id DESC", "limit" => $limit, "offset" => $offset, 'maxLimit' => $limit);

				if(!empty($request["sort"]))
					$paginate["sort"] = $request["sort"];
				if(!empty($request["sort_direction"]))
					$paginate["direction"] = $request["sort_direction"];

				$this->paginate = $paginate;

                $this->set("conditions", $conditions);
                $gbStatements = $this->paginate(); 
                $result_count = count($gbStatements); 
                
                if (!empty($gbStatements)) foreach ($gbStatements as $key => $gbStatement) {
                    $gbStatements[$key]["Statement"] = $this->report_format($gbStatements[$key]["Statement"], $request);
                }
                
                $gbStatements = $this->prepare_export($gbStatements, "index", $selColumns);
                $file = $this->batchcsv($gbStatements, $time, $offset, "statements");
               
                if ($result_count == $limit) {
                    $count += $result_count;
                } elseif ($result_count < $limit) {
                    $tmp_file_name = "statements-" . $time . ".csv";
		            if(!empty($request["csv"]) && (@$request["return_type"] == "path" || @$request["return_type"] == "url"))
		            {
		            	return $file;
		            }
                    else
                    	$this->out($file);
                }
            }
        } else { // for not csv
		$this->set("conditions", $conditions);
		$this->Statement->recursive = 0;
		$paginate = array("conditions" => $conditions, "order" => "id DESC", "limit" => $limit, 'maxLimit' => $limit);
		if(!empty($request["sort"]))
			$paginate["sort"] = $request["sort"];
		if(!empty($request["sort_direction"]))
			$paginate["direction"] = $request["sort_direction"];

		$this->paginate = $paginate;
        //$this->Paginator->settings = $this->paginate;
		$this->set("conditions", $conditions);
		$gbStatements = $this->paginate();

		if(!empty($gbStatements))
		foreach ($gbStatements as $key => $gbStatement) {
			$gbStatements[$key]["Statement"] = $this->report_format($gbStatements[$key]["Statement"], $request);
		}

		if (!empty($request["pdf"])) {
                $statement = $this->prepare_pdf_export($gbStatements, "index", $selColumns);
                $addons = new AddonsController;
                $export_pdf['header_image'] = 'header.png'; // pdf header image
                $export_pdf['footer_image'] = 'footer.png'; // pdf footer image
                $export_pdf['table_headers'] = $statement['headers']; // pdf table header
                $export_pdf['table_data'] = $statement['columns']; // pdf table data
                $export_pdf['report_type'] = $this->viewVars["page_title"]; // pdf file name
                $export_pdf['return_type'] = empty($request["return_type"])? 'download':$request["return_type"];  //path or url or download
                $export_pdf['filtered_by'] = $conditions;
	            $getPdf = $addons->generatePdf($export_pdf);
	            if(@$request["return_type"] == "path" || @$request["return_type"] == "url")
	            	return $getPdf;
         }
		if(!empty($request["group_attempts"])) {
			$exclude = array();

			if(!empty($request["group_attempts"]["exclude"])) {
				if(is_string($request["group_attempts"]["exclude"]))
					$exclude = array($request["group_attempts"]["exclude"]);
				else if(is_array($request["group_attempts"]["exclude"]))
					$exclude = $request["group_attempts"]["exclude"];
			}

			$gbStatements_return = array();
			if(!empty($gbStatements))
			foreach ($gbStatements as $key => $gbStatement) {
				if(!in_array($gbStatement["Statement"]["verb_id"], $exclude)) {
					$key = $gbStatement["Statement"]["agent_id"].":".$gbStatement["Statement"]["objectid"].":".$gbStatement["Statement"]["verb_id"];
					if(!empty($gbStatements_return[$key]["attempts"]))
						$gbStatements_return[$key]["attempts"]++;
					else
					{
						$gbStatements_return[$key] = $gbStatement["Statement"];
						$gbStatements_return[$key]["attempts"] = 1;
					}
				}
				else {
						$gbStatements_return[$key] = $gbStatement["Statement"];
						$gbStatements_return[$key]["attempts"] = 1;					
				}
			}
			$gbStatements = array();
			$i = 0;
			foreach ($gbStatements_return as $key => $gbStatement) {
				$gbStatements[$i] = array();
				$gbStatements[$i]["Statement"]	= $gbStatement;
				$i++;
			}
//			echo "<pre>"; print_r($gbStatements);exit;

		}
	}
		$this->set('gbStatements', $gbStatements);
		if(!empty($request["layout"]) && $request["layout"] == "ajax") {
			$this->layout = "ajax";
		}
	}
	function get_conditions($request = null) {
		global $db;
		$conditions = array();

		if(empty($request) || !is_array($request))
		$get = $_GET;
		else
		$get = $request;

		if(!empty($get["filter_id"]))
		{
			$filter_id = intval($get["filter_id"]);
			$filter = grassblade_config_get("filter_".$filter_id);
			if(!empty($filter["GET"]))
			$get = $get + $filter["GET"];
		}

		if(!empty($get["objectid"]))
			$conditions['objectid'] = $get["objectid"];
		if(!empty($get["n_objectid"]))
			$conditions['NOT']['objectid'] = $get["n_objectid"];
		if(!empty($get["object_like"]))
			$conditions[] = array("OR" => array("objectid LIKE '%".$get["object_like"]."%'", "object_definition_name LIKE '%".$get["object_like"]."%'", "object_definition_description LIKE '%".$get["object_like"]."%'"));
		if(!empty($get["verb_id"]))
			$conditions['verb_id'] = $get["verb_id"];
		if(!empty($get["agent_id"]))
			$conditions['agent_id'] = $get["agent_id"];
		if(!empty($get["agent_id_like"]))
			$conditions[] = "agent_id LIKE '%".$get["agent_id_like"]."%'";
		if(!empty($get["agent_like"]))
			$conditions[] = array("OR" => array("agent_name LIKE '%".$get["agent_like"]."%'", "agent_id LIKE '%".$get["agent_like"]."%'"));
		if(!empty($get["agent_name"]))
			$conditions[] = "agent_name LIKE '%".$get["agent_name"]."%'";
		if(!empty($get["agent_mbox"]))
			$conditions['agent_id'] = $get["agent_mbox"];
		if(!empty($get["agent_mbox_sha1sum"]))
			$conditions['agent_mbox_sha1sum'] = $get["agent_mbox_sha1sum"];
		if(!empty($get["agent_openid"]))
			$conditions['agent_openid'] = $get["agent_openid"];
		if(!empty($get["agent_account_homePage"]))
			$conditions['agent_account_homePage'] = $get["agent_account_homePage"];
        if(!empty($get["agent_account_name"]))
            $conditions['agent_account_name'] = $get["agent_account_name"];
		
		if(!empty($get["timestamp_start"]))
            $conditions['timestamp >='] = gmdate("c", strtotime($get["timestamp_start"]));//$get["timestamp_start"];
        if(!empty($get["timestamp_end"])) {
        	$time_element = intVal( date("His", strtotime($get["timestamp_end"])) );
        	$get["timestamp_end"] = empty($time_element)? gmdate("c", strtotime($get["timestamp_end"]) + 86400):gmdate("c", strtotime($get["timestamp_end"]));
            $conditions['timestamp <='] = $get["timestamp_end"];
        }
        
        if(!empty($get["score_min"]))
            $conditions['result_score_raw >='] = $get["score_min"];
        if(!empty($get["score_max"]))
            $conditions['result_score_raw <='] = $get["score_max"];
        
        if(!empty($get["score_percentage_min"]))
            $conditions['result_score_scaled >='] = $get["score_percentage_min"]/100;
        if(!empty($get["score_percentage_max"]))
            $conditions['result_score_scaled <='] = $get["score_percentage_max"]/100;
        

		if(!empty($get["parent_id"]))
			$conditions[] = "parent_ids LIKE CONCAT('%".$get["parent_id"]."%')";
		
		if(!empty($get["grouping_id"]))
            $conditions[] = "grouping_ids LIKE CONCAT('%".$get["grouping_id"]."%')";
        
        if(!empty($get["parent_or_grouping_id"]))
            $conditions[] = array("OR" => array("parent_ids" => $get["parent_or_grouping_id"], "grouping_ids" => $get["parent_or_grouping_id"]));
        //    $conditions[] = array("OR" => array("parent_ids LIKE " => "%".$get["parent_or_grouping_id"]."%", "grouping_ids LIKE "=> "%".$get["parent_or_grouping_id"]."%"));
        

		if(!empty($get["object_is_parent"]))
			$conditions[] = "parent_ids LIKE CONCAT('%', objectid ,'%')";
		if(!empty($get["object_is_group"]))
			$conditions[] = "grouping_ids LIKE CONCAT('%', objectid ,'%')";

		if(!empty($get["objectid"]) && !empty($get["related"])) {
			unset($conditions['objectid']);
            $objectids = is_string($get["objectid"])? array($get["objectid"]):$get["objectid"];
            $related_conditions = array();
            foreach ($objectids as $objectid) {
                $related_conditions[] = 'objectid = "'.$objectid.'"';
                $related_conditions[] = "parent_ids LIKE CONCAT('%', '".$objectid."' ,'%')";
                $related_conditions[] = "grouping_ids LIKE CONCAT('%', '".$objectid."' ,'%')"; 
            }
            $conditions[] = array("OR" => $related_conditions);
		}
		if(!empty($get["authority_user_id"]))
			$conditions['authority_user_id'] = $get["authority_user_id"];

		if(!empty($get["group"])) {
            if(!is_array($get["group"]))
			$conditions[] = "Statement.agent_id IN (SELECT DISTINCT agent_id FROM ".$db->config["prefix"]."gb_group_agents WHERE group_id = ".$db->value($get["group"]).")";
            else
            {
                $groups = array_map("intval", $get["group"]);
                $conditions[] = "Statement.agent_id IN (SELECT DISTINCT agent_id FROM ".$db->config["prefix"]."gb_group_agents WHERE group_id IN (".implode(",", $groups)."))";
            }
            
            foreach ($groups as $group) {
                $last_checked = grassblade_config_get("group_checked_".$group);

                if($last_checked < time() - 86400 ) {
                    if(!class_exists("Group"))
                    App::import('Model', 'Group');

                    $GroupModel = new Group();
                    $options = array('conditions' => array('Group.' . $GroupModel->primaryKey => $group));
                    $g = $GroupModel->find('first', $options);
                    if(!empty($g["Group"])) {
                        $updateGroup = $GroupModel->updateRemote($g["Group"]);
                    }
                    grassblade_config_set("group_checked_".$group, time());
                }
            }
        }

        if(!empty($get['content'])) {
        	$conditions = get_content_filter_conditions($get['content'], $conditions);
        }

		$this->set("filter_params", $get);
		return $conditions;		
	}
	public function attempts($request = null) {
		$this->set("page_title", __("Attempts Report"));

		if(empty($request) || !is_array($request))
			$request = $this->request_data($_REQUEST);

		$conditions_orig = $conditions = $this->get_conditions($request);
		$conditions['verb_id'] = "http://adlnet.gov/expapi/verbs/attempted";
		$conditions[] = array("OR" => array("parent_ids LIKE CONCAT('%', objectid ,'%')", "grouping_ids LIKE CONCAT('%', objectid ,'%')", "parent_ids" => "", "grouping_ids" => ""));

		$limit = !empty($request["limit"])? $request["limit"]:50;
		if(!empty($request["csv"]))
		$limit = 1000000;
		if (!empty($request['column_values'])) { // for pdf
            $limit = 1000000;
            $selColumns = explode(",", $request['column_values']);
        }        
        else
    	$selColumns = array();

		$this->set("conditions", $conditions);
		$this->Statement->recursive = 0;
		$paginate = array("conditions" => $conditions, "order" => "id DESC", "limit" => $limit, 'maxLimit' => 10000);
		
		if(!empty($request["sort"]))
			$paginate["sort"] = $request["sort"];
		if(!empty($request["sort_direction"]))
			$paginate["direction"] = $request["sort_direction"];

		$this->paginate = $paginate;
        //$this->Paginator->settings = $this->paginate;
		$gbStatements = $this->paginate();

		if(!empty($gbStatements))
		foreach ($gbStatements as $key => $gbStatement) {
			$completion = $this->get_attempt_completion_statement($gbStatements[$key]["Statement"]["agent_id"], $gbStatements[$key]["Statement"]["objectid"], $gbStatements[$key]["Statement"]["timestamp"]);
			if(is_array($completion)) {
				$tempStatement = $gbStatement;
				$gbStatement["Statement"] = $completion;
				$gbStatement["Statement"]["completion_timestamp"] = readable_timestamp($completion["timestamp"]);
				$gbStatement["Statement"]["timestamp"] = $tempStatement["Statement"]["timestamp"];
                $gbStatement["Statement"]["attempt_end"] = $completion["timestamp"];
			}
			else if(is_string($completion)) {
				$gbStatement["Statement"]["completion_timestamp"] = "-";
                $gbStatement["Statement"]["attempt_end"] = $completion;
			}
			else
			{
				$gbStatement["Statement"]["completion_timestamp"] = "-";
                $gbStatement["Statement"]["attempt_end"] = "-";
			}
            $gbStatement["Statement"]["attempt_start"] = $gbStatement["Statement"]["timestamp"];
			$gbStatements[$key]["Statement"] = $this->report_format($gbStatement["Statement"], $request);
		}
		if(!empty($request["csv"])) {
			$gbStatements = $this->prepare_export($gbStatements, "attempts", $selColumns);
			$file = $this->batchcsv($gbStatements, time(), 0, "attempts");
            if(@$request["return_type"] == "path" || @$request["return_type"] == "url")
				return $file;
            else
                $this->out($file);
		}
		if (!empty($request["pdf"])) {
            $statement = $this->prepare_pdf_export($gbStatements, "attempts", $selColumns);
           $addons = new AddonsController;
            $export_pdf['header_image'] = 'header.png'; // pdf header image
            $export_pdf['footer_image'] = 'footer.png'; // pdf footer image
            $export_pdf['table_headers'] = $statement['headers']; // pdf table header
            $export_pdf['table_data'] = $statement['columns']; // pdf table data
            $export_pdf['report_type'] = $this->viewVars["page_title"]; // pdf file name
            $export_pdf['return_type'] = empty($request["return_type"])? 'download':$request["return_type"];  //path or url or download
            $export_pdf['filtered_by'] = $conditions_orig;
            $getPdf = $addons->generatePdf($export_pdf);
            if(@$request["return_type"] == "path" || @$request["return_type"] == "url")
            	return $getPdf;
        }
		//print_r($gbStatements);
		$this->set('gbStatements', $gbStatements);
	}
	function get_attempt_completion_statement($agent_id, $objectid, $attempt_timestamp) {
		$conditions['verb_id'] = array();
		$conditions['verb_id'][] = "http://adlnet.gov/expapi/verbs/attempted";
		$conditions['verb_id'][] = "http://adlnet.gov/expapi/verbs/completed";
		$conditions['verb_id'][] = "http://adlnet.gov/expapi/verbs/passed";
		$conditions['verb_id'][] = "http://adlnet.gov/expapi/verbs/failed";
		$conditions['timestamp > '] =  $attempt_timestamp;
		$conditions['agent_id'] = $agent_id;
		$conditions['objectid'] = $objectid;
		$conditions[] = array("OR" => array("parent_ids LIKE CONCAT('%', objectid ,'%')", "grouping_ids LIKE CONCAT('%', objectid ,'%')", "parent_ids" => "", "grouping_ids" => ""));

		$gbStatementsCompletion = 	$this->Statement->find("first", array("conditions" => $conditions));
		if(empty($gbStatementsCompletion["Statement"]))
			return false;
		else if($gbStatementsCompletion["Statement"]["verb_id"] == "http://adlnet.gov/expapi/verbs/attempted")
			return $gbStatementsCompletion["Statement"]["timestamp"];
		else
			return $gbStatementsCompletion["Statement"];
	}
	public function attempts_summary($request = null) {
		$this->set("page_title", __("Attempts Summary"));

		if(empty($request) || !is_array($request))
			$request = $this->request_data($_REQUEST);

		$conditions_orig = $conditions = $this->get_conditions($request);
		$conditions['verb_id'] = "http://adlnet.gov/expapi/verbs/attempted";
		$conditions[] = array("OR" => array("parent_ids LIKE CONCAT('%', objectid ,'%')", "grouping_ids LIKE CONCAT('%', objectid ,'%')", "parent_ids" => "", "grouping_ids" => ""));

		$limit = !empty($request["limit"])? $request["limit"]:10;

		if (!empty($request['column_values'])) {
            $limit = 50;
            $selColumns = explode(",", $request['column_values']);
        }        
        else
    	$selColumns = array();

        if(!empty($request["csv"]) || !empty($request["pdf"])) {
            $limit = 10000;
        }

		$this->set("conditions", $conditions);
		$this->Statement->recursive = 0;
		$paginate = array("fields" => array("DISTINCT agent_id", "objectid", "verb_id"), "conditions" => $conditions, "order" => "id DESC", "limit" => $limit, "countField" => "DISTINCT agent_id, objectid, verb_id", 'maxLimit' => 10000);

		if(!empty($request["sort"]))
			$paginate["sort"] = $request["sort"];
		if(!empty($request["sort_direction"]))
			$paginate["direction"] = $request["sort_direction"];

		$this->paginate = $paginate;
        //$this->Paginator->settings = $this->paginate;
		$gbStatements = $this->paginate();


		if(!empty($gbStatements))
		foreach ($gbStatements as $key => $gbStatement) {
			$conditions = $this->get_conditions($request);
			$conditions = array(
							"agent_id"	=> $gbStatement["Statement"]["agent_id"],
							"objectid"	=> $gbStatement["Statement"]["objectid"],
							"verb_id"	=> $gbStatement["Statement"]["verb_id"],

						);
			$gbStatement = $this->Statement->find("first", array("conditions" => $conditions, "order" => "id ASC"));
			$gbStatement['Statement']['attempts'] = $this->Statement->find("count", array("conditions" => $conditions, "order" => "id ASC"));
			$gbStatements[$key] = $gbStatement;

			$conditions['verb_id'] = array();
			$conditions['verb_id'][] = "http://adlnet.gov/expapi/verbs/completed";
			$conditions['verb_id'][] = "http://adlnet.gov/expapi/verbs/passed";
			$conditions['verb_id'][] = "http://adlnet.gov/expapi/verbs/failed";

			$completion = $this->Statement->find("first", array("conditions" => $conditions, "order" => "id ASC"));
			if(!empty($completion) && is_array($completion["Statement"])) {
				$tempStatement = $gbStatement;
				$completion = $completion["Statement"];
				$gbStatement["Statement"] = $completion;
				$gbStatement["Statement"]["completion_timestamp"] = readable_timestamp($completion["timestamp"]);
				$gbStatement["Statement"]["timestamp"] = $tempStatement["Statement"]["timestamp"];
				$gbStatement["Statement"]["attempt_start"] = $tempStatement["Statement"]["timestamp"];	
				$gbStatement["Statement"]["attempt_end"] = $completion["timestamp"];				
				$gbStatement["Statement"]["attempts"] = $tempStatement["Statement"]["attempts"];				
			}
			else
			{
				$gbStatement["Statement"]["completion_timestamp"] = "-";
				$gbStatement["Statement"]["attempt_start"] = $gbStatement["Statement"]["timestamp"];	
				$gbStatement["Statement"]["attempt_end"] = "-";
		//		$gbStatement["Statement"]["attempts"] = $tempStatement["Statement"]["attempts"];		
			}
			$gbStatements[$key]["Statement"] = $this->report_format($gbStatement["Statement"], $request);
		}
		if(!empty($request["csv"])) {
			$gbStatements = $this->prepare_export($gbStatements, "attempts_summary", $selColumns);
			$file = $this->batchcsv($gbStatements, time(), 0, "attempts_summary");
            if(@$request["return_type"] == "path" || @$request["return_type"] == "url")
				return $file;
            else
                $this->out($file);
		}
		if (!empty($request["pdf"])) {
            $statement = $this->prepare_pdf_export($gbStatements, "attempts_summary", $selColumns);
           	$addons = new AddonsController;
            $export_pdf['header_image'] = 'header.png'; // pdf header image
            $export_pdf['footer_image'] = 'footer.png'; // pdf footer image
            $export_pdf['table_headers'] = $statement['headers']; // pdf table header
            $export_pdf['table_data'] = $statement['columns']; // pdf table data
            $export_pdf['report_type'] = $this->viewVars["page_title"]; // pdf file name
            $export_pdf['return_type'] = empty($request["return_type"])? 'download':$request["return_type"];  //path or url or download
            $export_pdf['filtered_by'] = $conditions_orig;
            $getPdf = $addons->generatePdf($export_pdf);
            if(@$request["return_type"] == "path" || @$request["return_type"] == "url")
            	return $getPdf;
         }
		$this->set('gbStatements', $gbStatements);
	}
	function request_data($request) {
		if(!empty($request["filter_id"]) && empty($request["sort"]) && empty($request["sort_direction"]))
		{
			$filter_id = intval($request["filter_id"]);
			$filter = grassblade_config_get("filter_".$filter_id);

			if(!empty($filter["GET"])) {
				if(empty($request["sort"]) && !empty($filter["GET"]["filter_email_sort_" . $filter_id]))
				$request["sort"] = $filter["GET"]["filter_email_sort_" . $filter_id];

				if(empty($request["sort_direction"]) && !empty($filter["GET"]["filter_email_sort_direction_" . $filter_id]))
				$request["sort_direction"] = $filter["GET"]["filter_email_sort_direction_" . $filter_id];
			}
		}
		return $request;
	}
	function report_format($statement, $request = null) {
		global $gbdb;
		if(!empty($statement["statement"])) {
			$s = json_decode($statement["statement"]);
		}

		if(!empty($statement['timestamp']))
		$statement["readable_timestamp"] = readable_timestamp($statement["timestamp"]);

		if(!empty($statement['agent_mbox'])) {
			$statement["agent_id"] = $statement['agent_mbox'];
			$statement["agent_params"] = "agent_id=".urlencode($statement['agent_mbox']);
		}
		if(!empty($statement['agent_mbox_sha1sum'])) {
			$statement["agent_id"] = $statement['agent_mbox_sha1sum'];
			$statement["agent_params"] = "agent_mbox_sha1sum=".urlencode($statement['agent_mbox_sha1sum']);
		}
		if(!empty($statement['agent_openid'])) {
			$statement["agent_id"] = $statement['agent_openid'];
			$statement["agent_params"] = "agent_openid=".urlencode($statement['agent_openid']);
		}
		if(!empty($statement['agent_account_homePage'])) {
			$statement["agent_id"] = $statement['agent_account_homePage']."/".$statement['agent_account_name'];
			$statement["agent_params"] = "agent_account_name=".urlencode($statement['agent_account_name'])."&agent_account_homePage=".urlencode($statement['agent_account_homePage']);
		}

		if(!empty($statement['object_definition_name']))
			$statement["object_name"] = $statement['object_definition_name'];
		else if(!empty($statement['object_definition_description']))
			$statement["object_name"] = $statement['object_definition_description']; 
		else if(!empty($statement['objectid']) && $translation = $gbdb->get_translation_name($statement['objectid'])) {
			$statement["object_name"] = $translation;
		}
		else if(!empty($statement['objectid']))
			$statement["object_name"] = $statement['objectid'];		
		else
			$statement["object_name"] = '';

		if(!empty($s) && !empty($s->object->definition->choices)) {
			$choices = array();
			foreach ($s->object->definition->choices as $choice) {
				$this_choice = null;
				if(!empty($choice->description))
				if(!empty($choice->description->{"en-US"}))
					$this_choice = $choice->description->{"en-US"};
				else if(!empty($choice->description->{"und"}))
					$this_choice = $choice->description->{"und"};
				else
					$this_choice = reset($choice->description);
				
				if(is_null($this_choice))
				$choices[] = @$choice->id;
				else
				$choices[] = $this_choice;	
			}
		
			$statement["choices"] = implode("\r\n", $choices);
		}
		else
			$statement["choices"] = "";

		if(!empty($s) && !empty($s->object->definition->correctResponsesPattern) && !empty($s->object->definition->correctResponsesPattern[0]) ) {
			$correctResponsesPattern = $s->object->definition->correctResponsesPattern[0];
			$correctResponsesPattern = explode("[,]", $correctResponsesPattern);
			if(!empty($s->object->definition->choices)) {
				$choices = array();
				foreach ($correctResponsesPattern as $pattern_id) {
					foreach ($s->object->definition->choices as $choice) {
						if(!empty($choice->id) && $choice->id == $pattern_id) {
							$this_choice = null;
							if(!empty($choice->description))
							if(!empty($choice->description->{"en-US"}))
								$this_choice = $choice->description->{"en-US"};
							else if(!empty($choice->description->{"und"}))
								$this_choice = $choice->description->{"und"};
							else
								$this_choice = reset($choice->description);
						
							if(is_null($this_choice))
							$choices[] = $pattern_id;
							else
							$choices[] = $this_choice;		
						}
					}
				}
				$statement["correct_response"] = implode("\r\n", $choices);
			}
			else
			if(!empty($s->object->definition->source) && !empty($s->object->definition->target)) {
				$matching_pairs = array();

				foreach ($correctResponsesPattern as $pattern_pair) {
					$pattern_pair = explode("[.]", $pattern_pair);
					if(count($pattern_pair) != 2)
						continue;

					$source_name = $target_name = "";
					foreach ($s->object->definition->source as $source) {
						if(!empty($source->id) && $source->id == $pattern_pair[0]) {
							$this_choice = null;
							if(!empty($source->description))
							if(!empty($source->description->{"en-US"}))
								$this_choice = $source->description->{"en-US"};
							else if(!empty($source->description->{"und"}))
								$this_choice = $source->description->{"und"};
							else
								$this_choice = reset($source->description);
						
							if(is_null($this_choice))
							$source_name = $pattern_pair[0];
							else
							$source_name = $this_choice;		
						}
					}
					foreach ($s->object->definition->target as $target) {
						if(!empty($target->id) && $target->id == $pattern_pair[1]) {
							$this_choice = null;
							if(!empty($target->description))
							if(!empty($target->description->{"en-US"}))
								$this_choice = $target->description->{"en-US"};
							else if(!empty($target->description->{"und"}))
								$this_choice = $target->description->{"und"};
							else
								$this_choice = reset($target->description);
						
							if(is_null($this_choice))
							$target_name = $pattern_pair[1];
							else
							$target_name = $this_choice;		
						}
					}
					$matching_pairs[] = $source_name . " => " . $target_name;
				}
				$statement["correct_response"] = implode("\r\n", $matching_pairs);
			}
			else
			$statement["correct_response"] = str_replace("[,]", "\r\n", $s->object->definition->correctResponsesPattern[0]);
		}
		else
		$statement["correct_response"] = "";

		$result = '';
		if(!empty($statement['result_success'])) 
		if($statement['result_success'] == 1 or $statement['result_success'] == "true")
			$result = ($statement["verb"] == "answered")? __("Correct"):__("Passed");
		else
			$result =  ($statement["verb"] == "answered")? __("Wrong"):__("Failed");			
		
		if(empty($result))
		if(!empty($statement['result_completion']) && ( $statement['result_completion'] == 1 or $statement['result_completion'] == "true"))
			$result = __("Completed");
		
		$statement["result"] = $result;

		if(!empty($s)) {
			$extensions = '';
			if(!empty($s->result->extensions))  
			{
				foreach ($s->result->extensions as $key => $value) {
					if(is_string($value))
						$value = maybe_to_time($value);
					if(is_bool($value))
						$value = ($value)? "true":"false";

					else if(is_object($value) || is_array($value)) 
						$value = json_encode($value);
					$key = explode("/extensions/", $key);
					$key = $key[count($key) - 1];
					$extensions .= "\n".$key. ': '.$value;
				}
			}
			if(!empty($s->context->extensions))  
			{
				foreach ($s->context->extensions as $key => $value) {
					if(is_object($value) || is_array($value)) 
						$value = json_encode($value);
					if(is_bool($value))
						$value = ($value)? "true":"false";
					
					$key = explode("/extensions/", $key);
					$key = $key[count($key) - 1];
					$extensions .= "\n".$key. ': '.$value;
				}
			}
			$statement["extensions"] = $extensions;
		}
		$statement['readable_result_duration'] = !empty($statement['result_duration'])? to_time($statement['result_duration']):"";

		$statement = modified("report_format", $statement, $request, $this);
		return $statement;
	}
	function prepare_export($export, $report, $selected_cloumns = null) {
		global $gbdb;
		switch ($report) {
			case 'index':
					$report_columns = array(
							"readable_timestamp",
							"agent_name",
							"agent_id",
							"verb",
							"object_name",
							"choices",
							"correct_response",
							"result",
							"result_score_raw",
							"result_score_scaled",
							"result_score_min",
							"result_score_max",
							"result_duration",
							"result_response",
							"extensions",
						);
					$exclude_fields = array(
										"statement",
										"agent_mbox_sha1sum",
										"agent_openid",
										"agent_account_homePage",
										"agent_account_name",
										"user_id",
										"object_definition_type",
										"headers",
										"voided",
										"created",
										"modified",
										"agent_params",
										"object_name"							
									);
					if(empty($selected_cloumns))
					foreach ($export as $key => $value) {
						if(isset($value["Statement"]))
						{
							$export[$key] = $value["Statement"];
							foreach ($exclude_fields as $ef) {
								unset($export[$key][$ef]);					
							}
							$statement = json_decode($value["Statement"]['statement']);
							$export[$key]["result_response"] = empty($statement->result->response)? "":$gbdb->get_response_translation_name($statement->result->response, true, " : ", $statement);
						}
						
						if(isset($export[$key]["correct_response"]))
						$export[$key]["correct_response"] = str_replace("\r\n", " : ", $export[$key]["correct_response"]);
						if(isset($export[$key]["choices"]))
						$export[$key]["choices"] = str_replace("\r\n", " : ", $export[$key]["choices"]);
					}
				break;

			case 'attempts':
					$report_columns = array(
							"agent_name",
							"agent_id",
							"object_name",
							"readable_timestamp",
							"completion_timestamp",
							"result",
							"result_score_raw",
							"result_score_scaled",
							"result_score_min",
							"result_score_max",
							"result_duration",
							"result_response",
							"extensions",
						);
					$included_fields = array(
										"Agent/User ID" => "agent_id",
										"User Name" => "agent_name",
										"Activity ID" => "objectid",
										"Activity Name" => "object_name",
										"Activity Description" => "object_definition_description",
										"Started On" => "readable_timestamp",
										"Completed On" => "completion_timestamp",
										"Response" => "result_response",
										"Time Spent" => "result_duration",
										"Time Spent (Readable)" => "readable_result_duration",
										"Scaled Score" => "result_score_scaled",					
										"Raw Score" => "result_score_raw",						
										"Min Score" => "result_score_min",						
										"Max Score" => "result_score_max",							
										"Completion" => "result_completion",							
										"Success" => "result_success",						
										"Parent IDs" => "parent_ids",
										"Grouping IDs" => "grouping_ids",	
									);
					if(empty($selected_cloumns))
					foreach ($export as $key => $value) {
						$val = array();
						if(isset($value["Statement"]))
						{
							foreach ($included_fields as $k => $f) {
								$val[$k] = @$value["Statement"][$f];
							}
						}
						$export[$key] = $val;
					}			
				break;
			case 'attempts_summary':
					$report_columns = array(
							"agent_name",
							"agent_id",
							"object_name",
							"readable_timestamp",
							"completion_timestamp",
							"attempts",
							"result",
							"result_score_raw",
							"result_score_scaled",
							"result_duration",
							"result_response",
							"extensions",
						);			
					$included_fields = array(
										"Agent/User ID" => "agent_id",
										"User Name" => "agent_name",
										"Activity ID" => "objectid",
										"Activity Name" => "object_name",
										"Activity Description" => "object_definition_description",
										"Started On" => "readable_timestamp",
										"Completed On" => "completion_timestamp",
										"No of Attempts" => "attempts",
										"Response" => "result_response",
										"Time Spent" => "result_duration",
										"Time Spent (Readable)" => "readable_result_duration",
										"Scaled Score" => "result_score_scaled",					
										"Raw Score" => "result_score_raw",						
										"Min Score" => "result_score_min",						
										"Max Score" => "result_score_max",							
										"Completion" => "result_completion",							
										"Success" => "result_success",						
										"Parent IDs" => "parent_ids",
										"Grouping IDs" => "grouping_ids",	
									);
					if(empty($selected_cloumns))
					foreach ($export as $key => $value) {
						$val = array();
						if(isset($value["Statement"]))
						{
							foreach ($included_fields as $k => $f) {
								$val[$k] = @$value["Statement"][$f];
							}
						}
						$export[$key] = $val;
					}			
				break;
			case 'activities':
					$report_columns = array(
							"object_name",
							"no_of_statements",
						);	
					$included_fields = array(
										"Activity ID" => "objectid",
										"Activity Name" => "object_name",
										"Activity Description" => "object_definition_description",
										"No of Statements" => "no_of_statements",
										"Parent IDs" => "parent_ids",
										"Grouping IDs" => "grouping_ids",
									);
					if(empty($selected_cloumns))
					foreach ($export as $key => $value) {
						$val = array();
						if(isset($value["Statement"]))
						{
							foreach ($included_fields as $k => $f) {
								$val[$k] = @$value["Statement"][$f];
							}
						}
						$export[$key] = $val;
					}	
					break;
			case 'agents':
					$report_columns = array(
							"agent_name",
							"agent_id",
							"no_of_statements",
						);	
					$included_fields = array(
										"Agent/User ID" => "agent_id",
										"User Name" => "agent_name",
										"No of Statements" => "no_of_statements",
									);
					if(empty($selected_cloumns))
					foreach ($export as $key => $value) {
						$val = array();
						if(isset($value["Statement"]))
						{
							foreach ($included_fields as $k => $f) {
								$val[$k] = @$value["Statement"][$f];
							}
						}
						$export[$key] = $val;
					}	
					break;
            case 'timespent':
                    $report_columns = array(
                            "agent_name",
                            "agent_id",
                            "timespent",
                            "readable_timespent",
                        );  
                    $included_fields = array(
                                        "Agent/User ID" => "agent_id",
                                        "User Name" => "agent_name",
                                        "Time Spent (sec)" => "timespent",
                                        "Time Spent" => "readable_timespent",
                                    );
                    if(empty($selected_cloumns))
                    foreach ($export as $key => $value) {
                        $val = array();
                        if(isset($value["Statement"]))
                        {
                            foreach ($included_fields as $k => $f) {
                                $val[$k] = @$value["Statement"][$f];
                            }
                        }
                        $export[$key] = $val;
                    }   
                    break;      							
			case 'verbs':
					$report_columns = array(
							"verb",
							"verb_id",
							"no_of_statements",
						);	
					$included_fields = array(
										"Verb" => "verb",
										"Verb ID" => "verb_id",
										"No of Statements" => "no_of_statements",
									);
					if(empty($selected_cloumns))
					foreach ($export as $key => $value) {
						$val = array();
						if(isset($value["Statement"]))
						{
							foreach ($included_fields as $k => $f) {
								$val[$k] = @$value["Statement"][$f];
							}
						}
						$export[$key] = $val;
					}	
					break;							
			default:
				break;
		}
		if(!empty($selected_cloumns))
		{
			$selected_cloumns_ids = array();
			foreach ($selected_cloumns as $selected_cloumn_key) {
				if($selected_cloumn_key == 1)
					continue;

				if(!empty($report_columns[$selected_cloumn_key - 2]))
				$selected_cloumns_ids[$selected_cloumn_key] = $report_columns[$selected_cloumn_key - 2];
			}
			foreach ($export as $key => $value) {
				$val = array();
				if(isset($value["Statement"]))
				{
					foreach ($selected_cloumns_ids as $k => $f) {
						$val[$f] = @$value["Statement"][$f];
					}
				}
				$export[$key] = $val;
			}
		}
		return $export;
	}
	function batchcsv($export, $time, $offset, $name = "download") {
		require_once(APP."Vendor".DS."parsecsv.lib.php");
		if(!is_dir(TMP."csv"))
			mkdir(TMP."csv");

		$csv = new parseCSV();
		$filename = TMP . "csv" . DS. $name .'-'.date("Y-m-d--H-i-s", $time).".csv";
        if ($offset == 0) {
            $csv->save($filename, $export, false, array_keys(reset($export)));
        } else {
            $csv->save($filename, $export, true, array_keys(reset($export)));
        }
        return $filename;
	}
    function csv($export, $file_name = "download") {
        require_once(APP."Vendor".DS."parsecsv.lib.php");
        $csv = new parseCSV();
        $csv->output (true, $file_name.'-'.date("Y-m-d--H-i-s").'.csv', $export, array_keys( reset( $export ) ));
        exit;
    }
    function prepare_pdf_export($export, $report, $selected_cloumns = null, $export_type = 'pdf') {     
     	global $gbdb;
     	  
        switch ($report) {
            case 'index':             
            	$included_fields = array("S.NO" => "counter", "Timestamp" => "readable_timestamp" , "User" => "agent_name", "User ID/Email" => "agent_id", "Verb" => "verb", "Activity" => "object_name", "Result" => "result_success", "Score" => "result_score_raw", "Percentage" => "result_score_scaled", "Min" => "result_score_min", "Max" => "result_score_max", "Time Spent" => "readable_result_duration", "Response" => "response", "Extra Info" => "extra_info");
            break;
            case 'attempts':                     
            	$included_fields = array("S.NO" => "counter", "User" => "agent_name", "User ID/Email" => "agent_id", "Activity" => "object_name", "Started" => "readable_timestamp", "Completed" => "completion_timestamp", "Result" => "result_success", "Score" => "result_score_raw", "Percentage" => "result_score_scaled", "Min Score" => "result_score_min", "Max Score" => "result_score_max", "Time Spent" => "readable_result_duration", "Response" => "response", "Extra Info" => "extra_info");
            break;
            case 'attempts_summary':
        		$included_fields = array("S.NO" => "counter", "User" => "agent_name", "User ID/Email" => "agent_id", "Activity" => "object_name", "Started" => "readable_timestamp", "Completed" => "completion_timestamp", "Attempts" => "attempts", "Result" => "result_success", "Score" => "result_score_raw", "Percentage" => "result_score_scaled", "Time Spent" => "readable_result_duration");
            break;
            case 'verbs':
                $included_fields = array("S.NO" => "counter", "Verb" => "verb", "Verb ID" => "verb_id", "Number of Statements" => "no_of_statements");
            break;                        
            case 'activities':
                $included_fields = array("S.NO" => "counter", "Activity" => "object_name", "Number of Statements" => "no_of_statements");
            break;                        
            case 'agents':
                $included_fields = array("S.NO" => "counter", "User" => "agent_name", "User Email/ID" => "agent_id" ,"Number of Statements" => "no_of_statements");
            break;                        
            case 'timespent':
                $included_fields = array("S.NO" => "counter", "User" => "agent_name", "User Email/ID" => "agent_id" ,"Time Spent" => "readable_timespent");
            break;
            default:
       		    return;
            break;           
        }
		if(empty($included_fields) || empty($selected_cloumns) || empty($export))
				return;
			 $val = array();            
        $column_counter = 1;
    	$filtered_column = array();                	 
    	foreach ($included_fields as $column_key => $column_value) {
    		if(in_array($column_counter, $selected_cloumns)) {
    			$filtered_column[$column_key] = $column_value;
    		}
    		$column_counter ++;  
       	}
        $counter = 1;

        foreach ($export as $key => $value) {
            if (isset($value["Statement"])) {
                foreach ($filtered_column as $k => $f) {
                	switch ($f) {                        		
                    	case 'counter':
                    		 $val[$key][] = $counter;
                    	break;
                    	case "result_success":
                    		$result = '';
        				if(!empty($value["Statement"]['result_success'])) 
        				if($value["Statement"]['result_success'] == 1 or $value["Statement"]['result_success'] == "true")
        					$result = (($value["Statement"]["verb"] == "answered")? "Correct":"Passed");
        				else
        					$result =  (($value["Statement"]["verb"] == "answered")? "Wrong":"Failed");			
        				
        				if(empty($result))
        				if(!empty($value["Statement"]['result_completion']) && $value["Statement"]['result_completion'] == 1 or $value["Statement"]['result_completion'] == "true")
        					$result =  "Completed";
        				
        				if(!empty($result))
        					$val[$key][] = $result;
        				else
        					$val[$key][] = '';
                    	break;
                    	case 'result_score_scaled':
                    	 $val[$key][] = number_format($value['Statement']['result_score_scaled']*100, 2)."%";
                    	break;
                    	case 'response':
                    		$statement =  json_decode($value['Statement']['statement']);
                    		if(!empty($statement->result->response))  
                    			$val[$key][] = $gbdb->get_response_translation_name($statement->result->response, true, " : ", $statement);
                    		else 
                    			$val[$key][] = '';
                    	break;
 						case 'choices':
 						case 'correct_response':
 							$val[$key][] = str_replace("\r\n", " : ", $export[$key][$f]);
 							break;
                    	case 'extra_info':
                    		$statement =  json_decode($value['Statement']['statement']);
                    		if(!empty($statement->result->extensions))  
        					{
        						$extra_info = array();
        						foreach ($statement->result->extensions as $extra_info_key => $extra_info_value) {
        						$extra_info[] = $extra_info_key. ': '.maybe_to_time($extra_info_value);
        						}									
        					}
        					if(!empty($extra_info))
        							$val[$key][] = implode(",", $extra_info);
        						else
        							$val[$key][] = '';
                    	break;
                    	default:
                        $val[$key][] = @$value["Statement"][$f];
                        break;
                    }
                }
            }
            $counter++;
        }
        $export_statements['headers'] = array_keys($filtered_column);
        $export_statements['columns'] = $val;
        return $export_statements;    
     }
     function out($file = null) {
     	if(!file_exists($file))
     		exit;
     	
        error_reporting(0);
        ini_set('display_errors', 0); 
     	ob_clean();
     	header("Content-type: application/force-download");
     	header('Content-Disposition: inline; filename="'.basename($file).'"');

     	readfile($file);
        exit;
     }
}
