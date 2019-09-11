<?php
App::uses('AppController', 'Controller');
/**
 * Triggers Controller
 *
 * @property Trigger $User
 */
class TriggersController extends AppController {
	public $uses = array("Trigger", "Statement");

	public function beforeFilter() {
		parent::beforeFilter();
		if(User::get('id') != "")
		$this->Auth->allow('edit', 'index', 'add', 'delete');
	}

	public function isAuthorized($user) {
		$ret = parent::isAuthorized($user);
		
		//$this->log($ret);
		return $ret;
	}

/**
 * index method
 *
 * @return void
 */
	public function index() {
		$this->Trigger->recursive = 0;
		$this->set('triggers', $this->paginate());
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		global $gbdb;
		if ($this->request->is('post')) {
			$this->Trigger->create();
			/*$target_ids = $this->request->data["Trigger"]["targets"];
			$targets = array();
			if(!empty($target_ids))
			foreach ($target_ids as $target_id) {
				if($target_id == "All") {
					$targets = array("All" => "All");
					break;
				}
				$postmeta_table = $gbdb->config["prefix"]."postmeta";
				$xapi_activity_id = $this->Trigger->query("SELECT meta_value FROM ".$postmeta_table." WHERE post_id = '".$target_id."' AND meta_key = 'xapi_activity_id'");
				if(!empty($xapi_activity_id[0][$postmeta_table]["meta_value"]))
				$targets[$xapi_activity_id[0][$postmeta_table]["meta_value"]] = $target_id;
			}
			$this->request->data["Trigger"]["target"] = serialize($targets);
			*/
			$target = array();
			$target["url"] = $this->request->data["Trigger"]["url"];
			$msg = '';
			if(!filter_var($target["url"], FILTER_VALIDATE_URL)) {
				$msg = __('Invalid URL. Trigger Deactivated.')." ";				
				$this->request->data["Trigger"]["status"] = 0;
			}
			else
				$this->request->data["Trigger"]["status"] = 1;

			$this->request->data["Trigger"]["target"] = serialize($target);
			$this->request->data["Trigger"]["created"] = date("Y-m-d H:i:s");
			$this->request->data["Trigger"]["modified"] = date("Y-m-d H:i:s");
			$this->request->data["Trigger"]["criterion"]["verb_id"] = $this->request->data["Trigger"]["verb_id"];
			$this->request->data["Trigger"]["criterion"]["result_success"] = @$this->request->data["Trigger"]["result_success"];
			$this->request->data["Trigger"]["criterion"]["result_completion"] = @$this->request->data["Trigger"]["result_completion"];
			$this->request->data["Trigger"]["criterion"] = serialize($this->request->data["Trigger"]["criterion"]);
			if ($trigger = $this->Trigger->save($this->request->data)) {
				if(empty($msg))
				$this->Session->setFlash(__('The trigger has been saved. '), 'default', array('class' => 'note note-success'));
				else
				$this->Session->setFlash($msg, 'default', array('class' => 'note note-danger'));
				
				$this->redirect(array('action' => 'edit', $trigger['Trigger']['id']));
			} else {
				$this->Session->setFlash($msg.__('The trigger could not be saved. Please, try again.'), 'default', array('class' => 'note note-danger'));
			}
		}
		/*$grassblade_contents = $grassblade_contents_selected = array();
		if($gbdb->table_exists('postmeta') && $gbdb->table_exists('posts')) {
			$posts_table = $gbdb->config["prefix"]."posts";
			$grassblade_contents_list = $this->Trigger->query("SELECT * FROM ".$posts_table." WHERE post_type = 'gb_xapi_content' AND post_status = 'publish'");	

			if(!empty($grassblade_contents_list[0][$posts_table])) 
			foreach($grassblade_contents_list as $key => $grassblade_content) {
				if(!empty($grassblade_content[$posts_table]["post_title"]))
				$grassblade_contents[$grassblade_content[$posts_table]["ID"]] = $grassblade_content[$posts_table]["post_title"];
			}
		}
		$this->set('grassblade_contents', $grassblade_contents);
		$this->set('grassblade_contents_selected', $grassblade_contents_selected);*/

		$last_statement_id = $this->Statement->find("first", array("order" => "ID DESC") );
		$last_statement_id = (int) @$last_statement_id["Statement"]["id"];
		$verbs = array();
		if($last_statement_id < 100000) {
			echo $last_statement_id;
			$verbs_list = $this->Statement->find("all", array("fields" => array("DISTINCT verb_id", "verb")));
			foreach ($verbs_list as $key => $verb) {
				if(!empty($verb["Statement"]["verb"]) && !empty($verb["Statement"]["verb_id"]))
				$verbs[$verb["Statement"]["verb_id"]] = $verb["Statement"]["verb"];
			}
		}
		else
		{
			$user_id = User::get("id");
			$config_key = "verbs.".$user_id;
			$stored_verbs = grassblade_config_get($config_key, array());
			foreach ($stored_verbs as $verb_id => $verb) {
				$verbs[$verb_id] = $verb["verb"];
			}
		}

		/*
		$verbs_list = $this->Statement->find("all", array("fields" => array("DISTINCT verb_id", "verb")));
		$verbs = array();
		foreach ($verbs_list as $key => $verb) {
			if(!empty($verb["Statement"]["verb"]) && !empty($verb["Statement"]["verb_id"]))
			$verbs[$verb["Statement"]["verb_id"]] = $verb["Statement"]["verb"];
		}
		*/
		if(empty($verb["http://adlnet.gov/expapi/verbs/passed"]))
			$verbs["http://adlnet.gov/expapi/verbs/passed"] = __("passed");
		if(empty($verb["http://adlnet.gov/expapi/verbs/failed"]))
			$verbs["http://adlnet.gov/expapi/verbs/failed"] = __("failed");
		if(empty($verb["http://adlnet.gov/expapi/verbs/completed"]))
			$verbs["http://adlnet.gov/expapi/verbs/completed"] = __("completed");

		$this->set('verbs', $verbs);

	}

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
		if (!$this->Trigger->exists($id)) {
			$this->Session->setFlash(__('Invalid trigger'), 'default', array('class' => 'note note-danger'));
			$this->redirect("/");
		}
			global $gbdb;
		if ($this->request->is('post') || $this->request->is('put')) {
			/*$target_ids = $this->request->data["Trigger"]["targets"];
			$targets = array();
			if(!empty($target_ids))
			foreach ($target_ids as $target_id) {
				if($target_id == "All") {
					$targets = array("All" => "All");
					break;
				}
				$postmeta_table = $gbdb->config["prefix"]."postmeta";
				$xapi_activity_id = $this->Trigger->query("SELECT meta_value FROM ".$postmeta_table." WHERE post_id = '".$target_id."' AND meta_key = 'xapi_activity_id'");
				if(!empty($xapi_activity_id[0][$postmeta_table]["meta_value"]))
				$targets[$xapi_activity_id[0][$postmeta_table]["meta_value"]] = $target_id;
			}*/
			$target = array();
			$target["url"] = $this->request->data["Trigger"]["url"];
			
			$msg = '';
			if(empty($this->request->data["Trigger"]["status"]))
				$this->request->data["Trigger"]["status"] = 0;

			if(!filter_var($target["url"], FILTER_VALIDATE_URL)) {
				$msg = __('Invalid URL. Trigger Deactivated.')." ";				
				$this->request->data["Trigger"]["status"] = 0;
			}

			$this->request->data["Trigger"]["url"] = @$target["url"];
			$this->request->data["Trigger"]["target"] = serialize($target);
			$this->request->data["Trigger"]["criterion"]["verb_id"] = $this->request->data["Trigger"]["verb_id"];
			$this->request->data["Trigger"]["criterion"]["authority_user_id"] = $this->request->data["Trigger"]["authority_user_id"];
			$this->request->data["Trigger"]["criterion"]["result_success"] = @$this->request->data["Trigger"]["result_success"];
			$this->request->data["Trigger"]["criterion"]["result_completion"] = @$this->request->data["Trigger"]["result_completion"];
			$this->request->data["Trigger"]["criterion"] = serialize($this->request->data["Trigger"]["criterion"]);
		if ($this->Trigger->save($this->request->data)) {
				if(empty($msg))
				$this->Session->setFlash(__('Trigger updated.'), 'default', array('class' => 'note note-success'));
				else
				$this->Session->setFlash($msg, 'default', array('class' => 'note note-danger'));

				$this->redirect(array('action' => 'edit', $id));
			} else {
				$this->Session->setFlash($msg.__('Update failed. Please, try again.'), 'default', array('class' => 'note note-danger'));
			}
		} else {
			$options = array('conditions' => array('Trigger.' . $this->Trigger->primaryKey => $id));
			$this->request->data = $this->Trigger->find('first', $options);
			$this->set("trigger", $this->request->data);
			/*$grassblade_contents = $grassblade_contents_selected = array();
			if($gbdb->table_exists('postmeta') && $gbdb->table_exists('posts')) {
				$posts_table = $gbdb->config["prefix"]."posts";
				$grassblade_contents_list = $this->Trigger->query("SELECT * FROM ".$posts_table." WHERE post_type = 'gb_xapi_content' AND post_status = 'publish'");	

				if(!empty($grassblade_contents_list[0][$posts_table])) 
				foreach($grassblade_contents_list as $key => $grassblade_content) {
					if(!empty($grassblade_content[$posts_table]["post_title"]))
					$grassblade_contents[$grassblade_content[$posts_table]["ID"]] = $grassblade_content[$posts_table]["post_title"];
				}
				$grassblade_contents_selected = unserialize($this->request->data["Trigger"]["target"]);

			}
			$this->set('grassblade_contents', $grassblade_contents);
			$this->set('grassblade_contents_selected', $grassblade_contents_selected);
			*/
			$target = unserialize(@$this->request->data["Trigger"]["target"]);
			$this->request->data["Trigger"]["url"] = @$target["url"];
			

			$last_statement_id = $this->Statement->find("first", array("order" => "ID DESC") );
			$last_statement_id = (int) @$last_statement_id["Statement"]["id"];
			$verbs = array();
			if($last_statement_id < 100000) {
				$verbs_list = $this->Statement->find("all", array("fields" => array("DISTINCT verb_id", "verb")));
				foreach ($verbs_list as $key => $verb) {
					if(!empty($verb["Statement"]["verb"]) && !empty($verb["Statement"]["verb_id"]))
					$verbs[$verb["Statement"]["verb_id"]] = $verb["Statement"]["verb"];
				}
			}
			else
			{
				$user_id = User::get("id");
				$config_key = "verbs.".$user_id;
				$stored_verbs = grassblade_config_get($config_key, array());
				foreach ($stored_verbs as $verb_id => $verb) {
					$verbs[$verb_id] = $verb["verb"];
				}
			}

			if(empty($verb["http://adlnet.gov/expapi/verbs/passed"]))
				$verbs["http://adlnet.gov/expapi/verbs/passed"] = __("passed");
			if(empty($verb["http://adlnet.gov/expapi/verbs/failed"]))
				$verbs["http://adlnet.gov/expapi/verbs/failed"] = __("failed");
			if(empty($verb["http://adlnet.gov/expapi/verbs/completed"]))
				$verbs["http://adlnet.gov/expapi/verbs/completed"] = __("completed");

			//$programs = $this->Trigger->Program->find('list');
			$criterion = unserialize($this->request->data["Trigger"]["criterion"]);

			$this->set('verbs', $verbs);
			$this->set('criterion', $criterion);

			App::import('Model', 'User');
			$User = new User();
			$authority_users_list = $User->find("all", array('recursive' => -1));
			$authority_users = array("" => __("All"));
			foreach ($authority_users_list as $authority_user) {
				$authority_users[$authority_user["User"]["id"]] =  $authority_user["User"]["id"].": ".$authority_user["User"]["name"];
			}
			$this->set('authority_users', $authority_users);


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
		$this->Trigger->id = $id;
		if (!$this->Trigger->exists()) {
			throw new NotFoundException(__('Invalid trigger'));
		}
		$this->request->onlyAllow('post', 'delete');
		if ($this->Trigger->delete()) {
			$this->Session->setFlash(__('Trigger deleted'), 'default', array('class' => 'note note-success'));
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Trigger was not deleted'), 'default', array('class' => 'note note-warning'));
		$this->redirect(array('action' => 'index'));
	}
}
