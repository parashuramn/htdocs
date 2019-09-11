<?php
App::uses('AppController', 'Controller');
/**
 * Statements Controller
 *
 * @property Statement $Statement
 */
class StatementsController extends AppController {


	 function beforeFilter()
	 {
		parent::beforeFilter();
		$this->redirect(array("controller" => "Reports", "action" => "dashboard"));
		//if(User::get("role") != "")
		//$this->Auth->allow('index', 'add', 'edit', 'delete');
	 }
/**
 * index method
 *
 * @return void
 */
	public function index() {
		$conditions = array();
		if(!empty($_GET["objectid"]))
			$conditions['objectid'] = $_GET["objectid"];
		if(!empty($_GET["verb_id"]))
			$conditions['verb_id'] = $_GET["verb_id"];
		if(!empty($_GET["agent_mbox"]))
			$conditions['agent_mbox'] = $_GET["agent_mbox"];
		if(!empty($_GET["agent_mbox_sha1sum"]))
			$conditions['agent_mbox_sha1sum'] = $_GET["agent_mbox_sha1sum"];
		if(!empty($_GET["agent_openid"]))
			$conditions['agent_openid'] = $_GET["agent_openid"];
		if(!empty($_GET["agent_account_homePage"]))
			$conditions['agent_account_homePage'] = $_GET["agent_account_homePage"];
		if(!empty($_GET["agent_account_name"]))
			$conditions['agent_account_name'] = $_GET["agent_account_name"];
		if(!empty($_GET["object_is_parent"]))
			$conditions[] = "parent_ids LIKE CONCAT('%', objectid ,'%')";
		if(!empty($_GET["object_is_group"]))
			$conditions[] = "grouping_ids LIKE CONCAT('%', objectid ,'%')";
		
		$this->set("conditions", $conditions);
		$this->Statement->recursive = 0;
		$this->paginate = array("conditions" => $conditions, "order" => "stored DESC", "limit" => 100);
		$gbStatements = $this->paginate();
		foreach ($gbStatements as $key => $gbStatement) {
			$gbStatements[$key]["Statement"] = $this->report_format($gbStatements[$key]["Statement"]);
		}
		//print_r($gbStatements);
		$this->set('gbStatements', $gbStatements);
	}
	function report_format($statement) {
		global $gbdb;
		$statement["timestamp"] = readable_timestamp($statement["timestamp"]);

		if(!empty($statement['agent_mbox'])) {
			$statement["agent_id"] = $statement['agent_mbox'];
			$statement["agent_params"] = "agent_mbox=".urlencode($statement['agent_mbox']);
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
		else if($translation = $gbdb->get_translation_name($statement['objectid'])) {
			$statement["object_name"] = $translation;
		}
		else if(!empty($statement['objectid']))
			$statement["object_name"] = $statement['objectid'];		

		return $statement;
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		if (!$this->Statement->exists($id)) {
			throw new NotFoundException(__('Invalid gb statement'));
		}
		$options = array('conditions' => array('Statement.' . $this->Statement->primaryKey => $id));
		$this->set('gbStatement', $this->Statement->find('first', $options));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			$this->Statement->create();
			if ($this->Statement->save($this->request->data)) {
				$this->Session->setFlash(__('The gb statement has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The gb statement could not be saved. Please, try again.'));
			}
		}
	}

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
		if (!$this->Statement->exists($id)) {
			throw new NotFoundException(__('Invalid gb statement'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->Statement->save($this->request->data)) {
				$this->Session->setFlash(__('The gb statement has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The gb statement could not be saved. Please, try again.'));
			}
		} else {
			$options = array('conditions' => array('Statement.' . $this->Statement->primaryKey => $id));
			$this->request->data = $this->Statement->find('first', $options);
		}
	}

/**
 * delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function delete($id = null) {
		$this->Statement->id = $id;
		if (!$this->Statement->exists()) {
			throw new NotFoundException(__('Invalid gb statement'));
		}
		$this->request->onlyAllow('post', 'delete');
		if ($this->Statement->delete()) {
			$this->Session->setFlash(__('Gb statement deleted'));
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Gb statement was not deleted'));
		$this->redirect(array('action' => 'index'));
	}
}
