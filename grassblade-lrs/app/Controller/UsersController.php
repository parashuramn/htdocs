<?php
App::uses('AppController', 'Controller');
/**
 * Users Controller
 *
 * @property User $User
 */
class UsersController extends AppController {


	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow('login', 'logout');
		
		if(User::get('id') != "")
		$this->Auth->allow('edit', 'logout');
	}

	public function isAuthorized($user) {
		$ret = parent::isAuthorized($user);
		
		//$this->log($ret);
		return $ret;
	}

	public function login() {
		$this->layout = "login";
		if ($this->request->is('post')) {
			
			$user = $this->User->find('first', array(
												'conditions' => array(	'User.email' => $this->request->data['User']['email'],
																		'User.password' => md5($this->request->data['User']['password']),
																	)
											)
							);
			
			if (!empty($user) && $this->Auth->login($user)) {
				$url = $this->Session->read('Auth.redirect');			
				if(!empty($url))
					$this->redirect($url);
				else
					$this->redirect(array('controller' => 'Reports', 'action' => 'dashboard'));
			} else {
				$this->Session->setFlash(__('Invalid email or password, try again'), 'default', array('class' => 'note note-danger'));
				$error_data = array(
						"type"			=> "Login",
						"user"			=> @$this->request->data['User']['email'],
						"request_method"=> "POST",
						"error_code" 	=> 401,
						"error_msg" 	=> __('Invalid email or password, try again'),
						"status"		=> 0
					);
				store_error_log($error_data);
			}
		}

		
		$this->set("return", $this->return_to());
	}
	private function return_to() {
		$return = @$_REQUEST['return'];
		if(empty($return))
		{
			$referer = @$_SERVER["HTTP_REFERER"];
			if(!empty($referer) && strpos($referer, Router::url("/", true)) !== false)
			{
				$return = $referer;
			}
		}
		return $return;
	}

	public function logout() {
		$this->Auth->logout();
		$this->redirect("/users/login?return=".rawurlencode($this->return_to()));
	}
/**
 * index method
 *
 * @return void
 */
	public function index() {
		$this->User->recursive = 0;
		$this->set('users', $this->paginate());
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			$email = trim( $this->request->data["User"]["email"] );
			$conditions = array( "email" => $email );
			$existing = $this->User->find("first", array("conditions" => $conditions ));

			if(empty($existing)) {
				$this->User->create();
				$this->request->data["User"]["created"] = date("Y-m-d H:i:s");
				$this->request->data["User"]["modified"] = date("Y-m-d H:i:s");
				$user = $this->User->save($this->request->data);
			}
			if (!empty($user)) {
				$this->Session->setFlash(__('The user account has been created.'), 'default', array('class' => 'note note-success'));
				$user_data = $this->request->data["User"];
				$user_data["password"] = "****";
				$error_data = array(
						"type"			=> "Users",
						"user"			=> @$this->request->data['User']['email'],
						"data"			=> $user_data,
						"request_method"=> "POST",
						"error_code" 	=> 200,
						"error_msg" 	=> __('The user account has been created.'),
						"status"		=> 1
					);
				store_error_log($error_data);

				$this->redirect(array('action' => 'edit', $user['User']['id']));
			} else {
				$user_data = $this->request->data["User"];
				$user_data["password"] = "****";
				$msg = __('The user could not be saved. Please, try again.');
				if(!empty($existing))
					$msg = __('User exists. ').$msg;
				$error_data = array(
						"type"			=> "Users",
						"user"			=> @$this->request->data['User']['email'],
						"data"			=> $user_data,
						"request_method"=> "POST",
						"error_code" 	=> 0,
						"error_msg" 	=> $msg,
						"status"		=> 0
					);
				store_error_log($error_data);

				$this->Session->setFlash(__('User exists. ').__('The user could not be saved. Please, try again.'), 'default', array('class' => 'note note-danger'));
			}
		}
		//$programs = $this->User->Program->find('list');
		//$this->set(compact('programs'));
	}

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
		if (!$this->User->exists($id)) {
			$this->Session->setFlash(__('Invalid user'), 'default', array('class' => 'note note-danger'));
			$this->redirect("/");
		}
		if(User::get("role") != "admin" && User::get("id") != $id)
		{
			$this->Session->setFlash(__('Permission Error'), 'default', array('class' => 'note note-danger'));
			$this->redirect("/");
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if(User::get("role") != "admin") {
				$this->request->data["User"]["role"] = "user";
				unset($this->request->data["User"]["permissions"]);
			}
		if(!empty($this->request->data["UsersAuth"]["add_auth"])) {

			$auth["id"] = null;
			$auth["api_user"] = $id."-".substr(md5($id.time()), 3, 15);
			$auth["api_pass"] = substr(md5(microtime()), 3, 25);
			$auth["auth"] = "Basic ".base64_encode($auth["api_user"].":".$auth["api_pass"]);
			$auth["user_id"] = $id;
			$this->User->UsersAuth->save(array("UsersAuth" => $auth));
			$this->Session->setFlash(__('A new AuthToken has been generated.'), 'default', array('class' => 'note note-success'));
			$this->redirect(array('action' => 'edit', $id));
		}
		if(!empty($this->request->data["UsersAuth"]["delete"])) {
			foreach($this->request->data["UsersAuth"]["delete"] as $key => $value) {
				if($value == "Delete") {
					$this->User->UsersAuth->delete($key);
					$this->Session->setFlash(__('AuthToken deleted.'), 'default', array('class' => 'note note-success'));
					$this->redirect(array('action' => 'edit', $id));
				}
			}
		}


		if(!empty($this->request->data["User"]["permissions"]))
		{
			$this->request->data["User"]["permissions"] = json_encode($this->request->data["User"]["permissions"]);
		}

			if ($this->User->save($this->request->data)) {
				$user_data = $this->request->data["User"];
				$user_data["password"] = "****";
				$error_data = array(
						"type"			=> "Users",
						"user"			=> @$this->request->data['User']['email'],
						"data"			=> $user_data,
						"request_method"=> "POST",
						"error_code" 	=> 200,
						"error_msg" 	=> __('Profile updated.'),
						"status"		=> 1
					);
				store_error_log($error_data);

				$this->Session->setFlash(__('Profile updated.'), 'default', array('class' => 'note note-success'));
				$this->redirect(array('action' => 'edit', $id));
			} else {
				$this->Session->setFlash(__('Update failed. Please, try again.'), 'default', array('class' => 'note note-danger'));
			}
		} else {
			$options = array('conditions' => array('User.' . $this->User->primaryKey => $id));
			$this->User->recursive = 1;
			$this->request->data = $this->User->find('first', $options);
			$this->request->data["User"]["password"] = "";
			$this->request->data["User"]["permissions"] = json_decode($this->request->data["User"]["permissions"]);
//print_r($this->request->data["User"] );exit;
			$this->set("user", $this->request->data);
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
		$this->User->id = $id;
		if (!$this->User->exists()) {
			throw new NotFoundException(__('Invalid user'));
		}
		$this->request->onlyAllow('post', 'delete');
		$user_data = (array) $this->User->find("first", array("conditions" => array("id" => $id)) );
		$user_data = $user_data["User"];

		if ($this->User->delete()) {
			$this->User->UsersAuth->deleteAll(array("user_id" => $id));
			$this->Session->setFlash(__('User deleted'), 'default', array('class' => 'note note-success'));

			$user_data["password"] = "****";
			$error_data = array(
					"type"			=> "Users",
					"user"			=> @$user_data['email'],
					"data"			=> $user_data,
					"request_method"=> "POST",
					"error_code" 	=> 200,
					"error_msg" 	=> __('User deleted'),
					"status"		=> 1
				);
			store_error_log($error_data);

			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('User was not deleted'), 'default', array('class' => 'note note-danger'));
		$this->redirect(array('action' => 'index'));
	}
}
