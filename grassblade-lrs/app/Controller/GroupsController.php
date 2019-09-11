<?php
App::uses('AppController', 'Controller');
/**
 * Groups Controller
 *
 * @property Group $Group
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class GroupsController extends AppController {

/**
 * Components
 *
 * @var array
 */
	public $components = array('Paginator', 'Session');

/**
 * index method
 *
 * @return void
 */
	public function index() {
		$importAll = $this->Group->importAll();
		//echo $this->Group->error;
		if(empty($importAll) && !empty($this->Group->error))
		{
			$this->Session->setFlash($this->Group->error, 'default', array('class' => 'note note-danger'));
		}
		$this->Group->recursive = 0;
		$this->set('Groups', $this->Paginator->paginate());
	}


/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			$this->Group->create();
			$this->request->data["Group"]["group_leaders"] = maybe_serialize($this->request->data["Group"]["group_leaders"]);
			$this->request->data["Group"]["created"] = date("Y-m-d H:i:s");
			$this->request->data["Group"]["modified"] = date("Y-m-d H:i:s");
			if ($this->Group->save($this->request->data)) {
				$this->Session->setFlash(__('The Group has been saved.'), 'default', array('class' => 'note note-success'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The Group could not be saved. Please, try again.'), 'default', array('class' => 'note note-danger'));
			}
		}
		App::import('Model', 'User');
		$UserModel = new User();
		$users_list = $UserModel->find("all");
		foreach ($users_list as $user) {
			$users[$user["User"]["id"]] = $user["User"]["id"].". ".$user["User"]["name"]." (".$user["User"]["email"].")";
		}
		$this->set("users", $users);
	}

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
		if (!$this->Group->exists($id)) {
			throw new NotFoundException(__('Invalid Group'));
		}
		if ($this->request->is(array('post', 'put'))) {
			//echo "<pre>";
			//print_r($this->request->data);

			$this->request->data["Group"]["group_leaders"] = maybe_serialize($this->request->data["Group"]["group_leaders"]);
			//print_r($this->request->data);
			//exit;
			if ($this->Group->save($this->request->data)) {
				$this->Session->setFlash(__('The Group has been saved.'), 'default', array('class' => 'note note-success'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The Group could not be saved. Please, try again.'), 'default', array('class' => 'note note-danger'));
			}
		} else {
			$options = array('conditions' => array('Group.' . $this->Group->primaryKey => $id));
			$this->request->data = $this->Group->find('first', $options);
			$updateGroup = $this->Group->updateRemote($this->request->data["Group"]);
			if(empty($updateGroup) && !empty($this->Group->error))
			{
				$this->Session->setFlash($this->Group->error, 'default', array('class' => 'note note-danger'));
			}

			$this->request->data = $this->Group->find('first', $options);
		}
		if($this->request->data["Group"]["type"] == "Local") {
			App::import('Model', 'User');
			$UserModel = new User();
			$users_list = $UserModel->find("all");
			foreach ($users_list as $user) {
				$users[$user["User"]["id"]] = $user["User"]["id"].". ".$user["User"]["name"]." (".$user["User"]["email"].")";
			}
			$this->set("users", $users);
		}

		$this->request->data["Group"]["group_leaders"] = maybe_unserialize($this->request->data["Group"]["group_leaders"]);
		modify("body_class", function($body_class, $view) {
			$type = $view->request->data["Group"]["type"];
			$type = str_replace(" ", "_", strtolower($type));
			$body_class[] = "group_type_".$type;
			return $body_class;
		}, 10, 2);
	}
	public function users($id = null) {
		$this->layout = "ajax";

		App::import('Model', 'Statement');
		App::import('Model', 'GroupAgent');



		if(!empty($_REQUEST["customActionType"]) && !empty($_REQUEST["customActionName"]) && !empty($_REQUEST["id"]) && $_REQUEST["customActionType"] == "group_action" && is_array($_REQUEST["id"])) {
			switch ($_REQUEST["customActionName"]) {
				case 'add':
					$this->Group->add_users($id, $_REQUEST["id"]);
					break;
				case 'remove':
					$this->Group->remove_users($id, $_REQUEST["id"]);
					break;				
				default:
					break;
			}

		}

		$db = ConnectionManager::getDataSource('default');
		$StatementM = new Statement();
		$GroupAgentM = new GroupAgent();

		$statementTable = $db->config["prefix"].$StatementM->useTable;
		$groupAgentTable = $db->config["prefix"].$GroupAgentM->useTable;




		$conditions = $order = array();
		$offset = empty($_REQUEST["start"])? 0:$_REQUEST["start"];
		$limit = empty($_REQUEST["length"])? 10:$_REQUEST["length"];
		$filterGA = $filterS = array("1 = 1");
		$agent_name = @$_REQUEST["agent_name"];

		if(!empty($agent_name))
		{
			$filterS[] = "agent_name LIKE ".$db->value("%".$agent_name."%");
		}

		if(!empty($_REQUEST["agent_id"]))
		{
			$filterS[] = $filterGA[] = "agent_id LIKE ".$db->value("%".$_REQUEST["agent_id"]."%");
		}

		$membership_status = @$_REQUEST["membership_status"];
		switch($membership_status)
		{
			case "member": 
				$query = "SELECT DISTINCT agent_id FROM ".$groupAgentTable." GroupAgent WHERE group_id =  ".$db->value($id)." AND ".implode(" AND ", $filterGA);

				if(!empty($agent_name))
				$query = "SELECT DISTINCT agent_id FROM ".$statementTable." GroupAgent WHERE ".implode(" AND ", $filterS)." AND agent_id IN (SELECT DISTINCT agent_id FROM ".$groupAgentTable." WHERE group_id = ".$db->value($id).")";

				break;
			case "nonmember": 
				$unionTable = "SELECT DISTINCT agent_id FROM  ".$groupAgentTable." where group_id <> ".$db->value($id)." AND ".implode(" AND ", $filterGA)." UNION  SELECT DISTINCT agent_id FROM  ".$statementTable." WHERE ".implode(" AND ", $filterGA);
				$query = "SELECT DISTINCT agent_id FROM (".$unionTable.") GroupAgent WHERE agent_id NOT IN (SELECT DISTINCT agent_id FROM ".$groupAgentTable." WHERE group_id = ".$db->value($id).") AND agent_id <> ''";

				if(!empty($agent_name))
				$query = "SELECT DISTINCT agent_id FROM ".$statementTable." GroupAgent WHERE ".implode(" AND ", $filterS)." AND agent_id NOT IN (SELECT DISTINCT agent_id FROM ".$groupAgentTable." WHERE group_id = ".$db->value($id).")";


				break;
			default: 
				$unionTable = "SELECT DISTINCT agent_id FROM  ".$groupAgentTable." where ".implode(" AND ", $filterGA)." UNION  SELECT DISTINCT agent_id FROM  ".$statementTable." WHERE ".implode(" AND ", $filterGA);
				$query = "SELECT DISTINCT agent_id FROM (".$unionTable.") GroupAgent WHERE agent_id <> ''";

				if(!empty($agent_name))
				$query = "SELECT DISTINCT agent_id FROM ".$statementTable." GroupAgent WHERE ".implode(" AND ", $filterS)."";

		}

		$countQuery = substr_replace($query, "SELECT COUNT(DISTINCT agent_id) as count ", 0, strlen("SELECT DISTINCT agent_id "));

		if($limit > 0)
		$query = $query . " LIMIT ".$offset.", ".$limit;

		$total_count = $StatementM->query($countQuery);
		$total = $filtered_count = intval(@$total_count[0][0]["count"]);

		$users_list = $StatementM->query($query);

		$agent_ids = array();

		foreach ($users_list as $key => $user) {
			$agent_ids[] = $user["GroupAgent"]["agent_id"];
		}
		$agent_names_list = $StatementM->find("all", array("fields" =>  array("DISTINCT Statement.agent_id", "Statement.agent_name"), "conditions" => array("Statement.agent_id" => $agent_ids)));
		$agent_names = array();
		if(!empty($agent_names_list))
		{
			foreach ($agent_names_list as $key => $value) {
				$agent_names[$value["Statement"]["agent_id"]] = $value["Statement"]["agent_name"];
			}
		}

		if($membership_status != "member" && $membership_status != "nonmember")
		{
			$group_members_list = $GroupAgentM->find("all", array("fields" =>  array("DISTINCT GroupAgent.agent_id"), "conditions" => array("GroupAgent.agent_id" => $agent_ids, "GroupAgent.group_id" => $id)));
			$group_members = array();
			if(!empty($group_members_list))
			foreach ($group_members_list as $key => $value) {
				$group_members[$value["GroupAgent"]["agent_id"]] = true;
			}
		}

		if(!empty($users_list))
		foreach ($users_list as $user) {
			$agent_id = $user["GroupAgent"]["agent_id"];
			$u[0] = '<input type="checkbox" name="id[]" value="'.$agent_id.'">';
			$u[1] = '';
			$statement = $StatementM->find("first", array("conditions" => array("Statement.agent_id" => $agent_id)));
			$u[2] = @$agent_names[$agent_id];
			$u[3] = $agent_id;

			if($membership_status == "member" || !empty($group_members[$agent_id]))
			$u[4] = '<span class="label label-sm label-success">'.__("Member").'</span>';
			else
			$u[4] = '<span class="label label-sm label-danger">'.__("Non-Member").'</span>';

			if(empty($agent_names[$agent_id]))
				$u[4] .= ' <span class="label label-sm label-warning">'.__("Not In LRS").'</span>';

			$u[5] = '';
			$data[$agent_id] = $u;
		}
		$sno = $offset;

		$users["data"] = array();

		if(!empty($data))
		foreach ($data as $d) {
			$d[1] = ++$sno;
 			$users["data"][] = $d;
		}
			$users["recordsFiltered"] = $filtered_count;
			$users["recordsTotal"] = $total;
			$users["draw"] = @$_POST["draw"];


		echo json_encode($users);
		exit;
	}
/**
 * delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function delete($id = null) {
		$this->Group->id = $id;
		if (!$this->Group->exists()) {
			throw new NotFoundException(__('Invalid Group'));
		}
		$this->request->allowMethod('post', 'delete');
		if ($this->Group->delete()) {
			//Have to Delete Group Users Now
			$this->Session->setFlash(__('The Group has been deleted.'), 'default', array('class' => 'note note-success'));
		} else {
			$this->Session->setFlash(__('The Group could not be deleted. Please, try again.'), 'default', array('class' => 'note note-danger'));
		}
		return $this->redirect(array('action' => 'index'));
	}
}
