<?php
/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
App::uses('Controller', 'Controller');
require_once(APP."Vendor".DS."hooks.php");
require_once(APP."Vendor".DS."input.php");
require_once(APP."Vendor".DS."functions.php");

global $global_permissions;
$global_permissions = grassblade_global_permission();
$global_permissions = modified("global_permissions", $global_permissions);

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package		app.Controller
 * @link		http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller {
	public $components = array(
		'Session',
		'DebugKit.Toolbar',
		'Auth' => array(
							'loginAction' => array(
												'controller' => 'users',
												'action' => 'login',
												),
							'authenticate' => array(
													'Form' => array(
														'fields' => array('email' => 'email'),
													)
												),
							'authorize' => array('Controller'),							
						)
	);
	public function beforeFilter() {
		modified("app_start", '', $this);

		global $Input;
		$this->verifyLRSConfiguration();
		$this->gb_init();
		$this->sso();
		$Output = new Output();
		if(@$_SERVER['REQUEST_METHOD'] == "OPTIONS")
			$Output->send(200, '');
	
		$is_xapi_request = modified("is_xapi_request", $this->request->params["controller"] == "xAPI", $this);

		if(!empty($is_xapi_request))
		{
			$Input = new Input();
		}

		App::import('Model', 'User');
		$userModel = new User();
		
		if(!empty($Input->headers["Authorization"])) {
            if(!strpos(base64_decode(str_replace("Basic ", "", @$Input->headers["Authorization"])), ":"))
                    $Output->send(400, "Error: Malformed Request.");

                if(!strpos($Input->headers["Authorization"], "="))
                        $Input->headers["Authorization"] .= "=";

			App::import('Model', 'UsersAuth');
			$UsersAuth = new UsersAuth();
			$auth = $UsersAuth->find("first", array("conditions" => array("auth" => trim($Input->headers["Authorization"]))));
			
			if(!empty($auth))
			$auth = $auth["UsersAuth"];
			else
			$auth = gb_secure_tokens($UsersAuth, $Input, trim($Input->headers["Authorization"]));
			
			if(!empty($auth))
			$user = $userModel->find("first", array("conditions" => array("User.id" => $auth["user_id"])));
			
			if(!empty($user))
			{
				$user["User"]["auth"] = $auth;
			}
		}
		if(empty($user)) $user = $this->Auth->user();
		if(empty($user)) $user = array('id' => '', 'name' => '', 'role' => '', 'username' => '', "email" => '', "auth" => '');
		
		if(!empty($user['User']['role']) && $user['User']['role'] == 'admin')
		$this->Auth->allow('index', 'view','edit','add', 'delete', 'users', 'sql_explain');
		else
		$this->Auth->allow('sql_explain');

		if(empty($user["id"]))
		{
			if($this->action != "login" && $this->action != "logout")
			$this->Session->write('Auth.redirect', $this->request->here(false));
			else				
			if(!empty($_REQUEST["return"]))
			$this->Session->write('Auth.redirect', $_REQUEST["return"]);				
		}
		User::store($user);
		modified("init", '', $this);
	}
	public function verifyLRSConfiguration() {
		if(!defined("DATABASE_FILE")) {
			define("DATABASE_FILE", APP."Config".DS."database.php");
		} 

		if(!file_exists(DATABASE_FILE) && ($this->request->params["controller"] != "Configure" || $this->request->params["action"] != "Database")) {
			$this->redirect(array("controller" => "Configure", "action" => "Database"));
			exit;
		}
	}
	private function sso() {
		if(!empty($_GET["external_ip"])) {
			echo $_SERVER["REMOTE_ADDR"];
			exit;	
		}
		if(!empty($_GET["api_user"]) && !empty($_GET["api_pass"])) {
			$Integrations = grassblade_config_get("Configure_Integrations");
			$sso_hosts = array_map("gethostbyname", array_map("trim", explode(",", @$Integrations["sso"]["host"])));
			$parse_url = parse_url(Router::url("/", true));

			$external_ip = file_get_contents_curl(Router::url("/Configure/?external_ip=1", true));
			$sso_hosts[] = gethostbyname(trim($external_ip));
			$sso_hosts[] = gethostbyname($parse_url['host']);

			$REMOTE_ADDR = ($_SERVER["REMOTE_ADDR"] == "::1")? "127.0.0.1":$_SERVER["REMOTE_ADDR"];
			$HTTP_X_FORWARDED_FOR = @$_SERVER["HTTP_X_FORWARDED_FOR"];
			if(!in_array($REMOTE_ADDR, $sso_hosts) && !in_array($HTTP_X_FORWARDED_FOR, $sso_hosts)) {
				echo 'Invalid Access';
				exit;
			}
		
			App::import('Model', 'UsersAuth');
			$UsersAuth = new UsersAuth();
			$auth = $UsersAuth->find("first", array("conditions" => array("api_user" => $_GET["api_user"], "api_pass" => $_GET["api_pass"])));

			if(!empty($auth["UsersAuth"]["user_id"])) {
				$sso_auth_token = md5(time().$auth["UsersAuth"]["user_id"]);
				Configure::restore("sso_auth");
				$sso_auth = Configure::read("sso_auth");

				if(is_array($sso_auth) && count($sso_auth)) 
				foreach($sso_auth as $token => $sso_auth_one) {
					if(empty($sso_auth_one["timestamp"]) || $sso_auth_one["timestamp"] < time() - 60)
						unset($sso_auth[$token]);
				}
				 
				$sso_auth[$sso_auth_token] = array(
						"user_id" => $auth["UsersAuth"]["user_id"],
						"sso_auth_token" => $sso_auth_token,
						"timestamp" => time()
					);

				Configure::write("sso_auth", $sso_auth);
				Configure::store("sso_auth");
				$Output = new Output();
				$Output->send(200, json_encode($sso_auth[$sso_auth_token]), "json");
				exit;
			}
			exit;
		}
		if(!empty($_GET["sso_auth_token"])) {
			$sso_auth_token = $_GET["sso_auth_token"];
			Configure::restore("sso_auth");
			$sso_auth = Configure::read("sso_auth");

			if(!empty($sso_auth[$sso_auth_token]["user_id"]) && !empty($sso_auth[$sso_auth_token]["sso_auth_token"]) && !empty($sso_auth[$sso_auth_token]["timestamp"]) && $sso_auth[$sso_auth_token]["timestamp"] > time() - 60 && $sso_auth[$sso_auth_token]["sso_auth_token"] == $_GET["sso_auth_token"]) {
				App::import('Model', 'User');
				$userModel = new User();
				$user = $userModel->find("first", array("conditions" => array("id" => $sso_auth[$sso_auth_token]["user_id"]), "recursive" => 2));
				if(!empty($user["User"])) {
					$this->Auth->login($user);
					if(!empty($_GET["redirect_url"]))
					$this->redirect($_GET["redirect_url"]);
					else {
						$url = Router::fullBaseUrl().Router::url(array('controller' => "Reports", "action" => "dashboard"));
						$protocol = (env('HTTPS') || env('HTTP_X_FORWARDED_PROTO') == "https")? "https":"http";
						$not_protocol = ($protocol == "https")? "http":"https";
						$url = str_replace($not_protocol."://", $protocol."://", $url);
						$this->redirect( $url );
					}
					exit;
				}
			}
		}
	}
	public function gb_init() {
		require_once(APP."Vendor".DS."input.php");
		require_once(APP."Vendor".DS."output.php");
		global $license;

		if(!file_exists(DATABASE_FILE))
		{
            if(!defined("GBDB_DATABASE_CONNECT_ERROR"))
			define("GBDB_DATABASE_CONNECT_ERROR", 1);			
		}
		else
		try{
			global $db, $gbdb;
			App::uses('ConnectionManager', 'Model');
			$db = ConnectionManager::getDataSource('default');
			include_once(APP."Vendor".DS."gbdb.php");
			$gbdb = new GBDB();
		}
		catch (Exception $e)
		{
			if(!defined("GBDB_DATABASE_CONNECT_ERROR"))
			define("GBDB_DATABASE_CONNECT_ERROR", $e->getMessage());

			return;
		}

		if($this->request->params["controller"] != "xAPI") {
			include_once(APP."Vendor".DS."arraytotable.php");
			if($this->request->params["controller"]."/".$this->request->params["action"] == "Configure/License")
				$license = grassblade_load_license(true);
			else 
			{
				$license = grassblade_load_license();
				if(empty($license) && in_array($this->request->params["controller"], array("Reports", "Triggers")))
				{
					$this->redirect(array("controller" => "Configure", "action" => "License"));
					exit;
				}
			}
		}
	}
	public function isAuthorized($user) {
		// Admin can access every action
		if (isset($user['role']) && $user['role'] === 'admin') {
				return true;
		}
		// Default deny
		return false;
	}
	/**
    * override paginate function.
    *
    * override default paginate functionality to fix out of range limit.
    */
    public function paginate($object = null, $scope = array(), $whitelist = array()) {
        try {
            return $this->Components->load('Paginator', $this->paginate)->paginate($object, $scope, $whitelist);
        }
        catch(NotFoundException $e) {
            return array();
        }
    }
}
