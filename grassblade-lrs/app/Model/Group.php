<?php
App::uses('AppModel', 'Model');
/**
 * Group Model
 *
 * @property Program $Program
 */
class Group extends AppModel {

	public $useTable = 'gb_groups';


/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'name';

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'name' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
	);

	public function add_users($group_id, $agent_ids) {
		if(!is_array($agent_ids))
			$agent_ids = array($agent_ids);

		if(count($agent_ids) < 1)
			return;
		//if(!count($agent_ids))
		//	return;

		App::import('Model', 'GroupAgent');
		$GroupAgentM = new GroupAgent();
		foreach ($agent_ids as $agent_id) {
			$group_agent = array(
					"group_id" => $group_id,
					"agent_id" => $agent_id
				);
			$group_agents[] = $group_agent;
		}
		$GroupAgentM->saveAll($group_agents);
	}
	public function remove_users($group_id, $agent_ids) {
		if(!is_array($agent_ids))
			$agent_ids = array($agent_ids);

		if(count($agent_ids) < 1)
			return;

		App::import('Model', 'GroupAgent');
		$GroupAgentM = new GroupAgent();

		$conditions = array(
				"GroupAgent.group_id" => $group_id,
				"GroupAgent.agent_id" => $agent_ids
			);
		$GroupAgentM->deleteAll($conditions);
	}
	public function update_users($group_id, $agent_ids) {
		if(!is_array($agent_ids))
			return;

		App::import('Model', 'GroupAgent');
		$GroupAgentM = new GroupAgent();
		$agents_list = $GroupAgentM->find("all", array("conditions" => array("group_id" => $group_id)));

		$current_agents = array();
		if(!empty($agents_list))
		foreach ($agents_list as $key => $value) {
			$current_agents[$value["GroupAgent"]["agent_id"]] = $value["GroupAgent"]["agent_id"];
		}

		$add_agents = array();
		foreach($agent_ids as $agent_id) {
			if(!in_array($agent_id, $current_agents))
			{
				$add_agents[] = $agent_id;
			}
			unset($current_agents[$agent_id]);
		}

		$this->add_users($group_id, $add_agents);
		$this->remove_users($group_id, $current_agents);
	}

	public function importAll() { //return;
		try
		{
			$wp = get_wordpress_client();
			//print_r($wp);
			if(empty($wp) || is_string($wp)) {
				if(is_string($wp))
				$this->error = $wp;
				return false;
			}

			//$remote_groups = $wp->callCustomMethodSec("grassblade.getGroups", array("posts_per_page" => -1, "leaders_list" => 1));
			$remote_groups = call_wordpress_api($wp, "getGroups", array("posts_per_page" => -1, "leaders_list" => 1));

			//echo "<pre>"; print_r($remote_groups); echo "</pre>";
			if(empty($remote_groups) || is_string($remote_groups)) {
				if(is_string($remote_groups))
					$this->error = $remote_groups;
				return;
			}

			$groups_list = $this->find("all", array("conditions" => array("type" => "GrassBlade xAPI Companion")));

			if(empty($groups_list))
				$groups = array();
			else
			foreach ($groups_list as $key => $value) {
				$groups[$value["Group"]["remote_id"]] = $value["Group"];
			}
			foreach ($remote_groups as $remote_group) {
				$remote_group = (array) $remote_group;
				$group = @$groups[$remote_group["ID"]];
				if(!is_array($remote_group["group_leaders"]) && !is_object($remote_group["group_leaders"]))
					$remote_group["group_leaders"] = array();
				else
					$remote_group["group_leaders"] = (array) $remote_group["group_leaders"];

				if(empty($group))
				{
					$new_group = array(
							"name" 			=> $remote_group["name"],
							"type" 			=> "GrassBlade xAPI Companion",
							"remote_id" 	=> $remote_group["ID"],
							"group_leaders" => maybe_serialize($remote_group["group_leaders"]),
							"created"		=> 	 date("Y-m-d H:i:s"),
							"modified"		=> 	 date("Y-m-d H:i:s"),
						);
					$this->create();
					$this->save($new_group);
				}
				else
				{
					if(!is_array($group["group_leaders"]))
						$group["group_leaders"] = maybe_unserialize($group["group_leaders"]);

					if($remote_group["name"] != $group["name"] || @$remote_group["group_leaders"] != @$group["group_leaders"])
					{
						$group["name"] = $remote_group["name"];
						$group["group_leaders"] = maybe_serialize($remote_group["group_leaders"]);
						$group["modified"] = date("Y-m-d H:i:s");
						$this->save($group);
					}
				}
				unset($groups[$remote_group["ID"]]);
			}

			if(!empty($remote_groups) && !empty($groups)) {
				$ids = array();
				foreach ($groups as $key => $group) {
					$ids[] = $group["id"];
				}
				$this->deleteAll(array("id" => $ids));
				$this->deleteAll(array("type" => "GrassBlade xAPI Companion",  "remote_id < 1") );
				$this->deleteAll(array("type" => "GrassBlade xAPI Companion",  "remote_id" => null) );
			}
			return true;

		}
		catch (Exception $e)
		{	
			$code = $e->getCode();
			$message = $e->getMessage();
			if(strlen($message) > 100) {
				$this->error = __("404 Not Found. Your wordpress url could be wrong.");
			}
			else {
			 $this->error =  "Error Code: ". $code.", Error: ".$message;
			}
			return false;
		}	

	}

	public function updateRemote($group) {
		if(empty($group["remote_id"]) || $group["type"] != "GrassBlade xAPI Companion")
			return;

			try
			{
				$wp = get_wordpress_client();
				if(empty($wp) || is_string($wp))
					return;

				//$remote_group = $wp->callCustomMethodSec("grassblade.getGroups", array("id" => $group["remote_id"], "posts_per_page" => -1, "leaders_list" => 1, "users_list" => 1));
				$remote_group = call_wordpress_api($wp, "getGroups", array("id" => $group["remote_id"], "posts_per_page" => -1, "leaders_list" => 1, "users_list" => 1));

				$this->updateGroup($group, $remote_group, 1, 1);
			}
			catch (Exception $e)
			{	
				$code = $e->getCode();
				$message = $e->getMessage();
				if(strlen($message) > 100)
					$this->error =  __("404 Not Found. Your wordpress url could be wrong.");
				else
				 $this->error =  "Error Code: ". $code.", Error: ".$message;
				return false;
			}	

	}
	private function update_leaders($group, $remote_group) {
		/* Update Group Name if changed on remote site */

		if(!is_array($group["group_leaders"]))
			$group["group_leaders"] = maybe_unserialize($group["group_leaders"]);

		if(@$remote_group["group_leaders"] != @$group["group_leaders"])
		{
			$group["group_leaders"] = maybe_serialize($remote_group["group_leaders"]);
			$this->save($group);
		}
		/* Update Group Name if changed on remote site */		
	}
	private function updateGroup($group, $remote_group, $update_leaders = 0, $update_users = 0) {
		if(empty($group["id"]) || empty($group["remote_id"]))
			return;

		if(isset($remote_group["group_leaders"]))
		$remote_group["group_leaders"] = (array) $remote_group["group_leaders"];

		if(isset($remote_group["group_users"]))
		$remote_group["group_users"] = (array) $remote_group["group_users"];


		/* Delete Group if group doesn't exist on remote site */
		if(empty($remote_group)) {
			$this->delete($group["id"]);
			return;
		}

		if($remote_group["name"] != $group["name"])
		{
			$group["name"] = $remote_group["name"];
			$this->save($group);
		}

		if($update_leaders)
		{
			$this->update_leaders($group, $remote_group);
		}

		/* Update group agents */
		if($update_users) {
			$remote_group_users = $remote_group["group_users"];
			$this->update_users($group["id"], $remote_group_users);
		}
	}
	public function groups_by_leader($email) {
		if(!is_numeric($email)) {
    		App::import("model", "User");
    		$userModel = new User();
    		$user = $userModel->find("first", array("conditions" => array("email" => $email)));
    		$id = $user["User"]["id"];
		}
		$groups_list = $this->find("all", array("fields" => array("id", "group_leaders")));
		if(empty($groups_list))
			return array();

		foreach ($groups_list as $key => $value) {
			$group = $value["Group"];
			$group_leaders = maybe_unserialize($group["group_leaders"]);
			if(in_array($email, $group_leaders) || !empty($id) &&  in_array($id, $group_leaders))
				$groups[] = $group["id"];
		}
		return $groups;
	}
	//public $hasMany = array("GroupAgent" => array('foreignKey' => 'agent_id'));
}
