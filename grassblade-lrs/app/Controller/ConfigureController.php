<?php
/**
 * Static content controller.
 *
 * This file will render views from views/Configure/
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
App::uses('AppController', 'Controller');

/**
 * Static content controller
 *
 * Override this controller by placing a copy in controllers directory of an application
 *
 * @package       app.Controller
 * @link http://book.cakephp.org/2.0/en/controllers/Configure-controller.html
 */
class ConfigureController extends AppController {

	 function beforeFilter()
	 {
		parent::beforeFilter();
//		if(!file_exists(DATABASE_FILE))
		$this->Auth->allow('Database');
		$this->Auth->allow('cron');
	 }
	 public function isAuthorized($user) {
		return true;
	}
/**
 * Controller name
 *
 * @var string
 */
	public $name = 'Configure';

/**
 * This controller does not use a model
 *
 * @var array
 */
	public $uses = array();

/**
 * Displays a view
 *
 * @param mixed What page to display
 * @return void
 */
	public function display() {
		$path = func_get_args();

		$count = count($path);
		if (!$count) {
			$this->redirect('/');
		}
		$page = $subpage = $title_for_layout = null;

		if (!empty($path[0])) {
			$page = $path[0];
		}
		if (!empty($path[1])) {
			$subpage = $path[1];
		}
		if (!empty($path[$count - 1])) {
			$title_for_layout = Inflector::humanize($path[$count - 1]);
		}
		$this->set(compact('page', 'subpage', 'title_for_layout'));
		$this->render(implode('/', $path));
	}
	public function Database() {
		$this->set("page_title", __("Database Configuration"));
		if(defined("GBDB_DATABASE_CONNECT_ERROR")) {
			$this->Session->setFlash(__('Could not connect to database.'), 'default', array('class' => 'note note-warning'));

			if($this->request->is('post')) {
				$host = $this->request->data['Configure']['host'];
				$user = $this->request->data['Configure']['username'];
				$pass = $this->request->data['Configure']['password'];
				$db = $this->request->data['Configure']['database_name'];
				$database_prefix = $this->request->data['Configure']['database_prefix'];
				$db_status = grassblade_verify_db($host, $user, $pass, $db);
				switch($db_status) {
					case 0:
							$this->Session->setFlash(__('Database details verified successfully.'), 'default', array('class' => 'note note-success'));	
							$database_file_content = "<?php 
	class DATABASE_CONFIG {
		public \$default = array(
			'datasource' => 'Database/Mysql',
			'persistent' => false,
			'host' => '".$host."',
			'login' => '".$user."',
			'password' => '".$pass."',
			'database' => '".$db."',
			'prefix' => '".$database_prefix."',
			'encoding' => 'utf8',
		);
	}";
							$ret = file_put_contents(DATABASE_FILE, $database_file_content);
							if(empty($ret)) {
								$this->Session->setFlash(__('Database details verified successfully but could not write database configuration file.'), 'default', array('class' => 'note note-warning'));	
								$this->set("database_file_content", $database_file_content);
							}
							else
							{
								$this->Session->setFlash(__('Database file configured successfully.'),'default', array('class' => 'note note-success'));	
								$this->redirect(array("controller" => "Configure", "action" => "Database"));
								exit;
							}
							break;
					case 1:			
							$this->Session->setFlash(__('Could not connect to mysql server. Wrong database Host, Username, or Password.'), 'default', array('class' => 'note note-warning'));
							break;
					case 2:
							$this->Session->setFlash(sprintf(__('Could not select database. Wrong database name %s, or user %s doesn\'t have access to database.'), "<i>".$db."</i>", "<i>".$user."</i>"), 'default', array('class' => 'note note-warning'));
							break;
				}
			}
		}
		else 
		{
			$this->Session->setFlash(__('Database configured and connected.'),'default', array('class' => 'note note-success'));
			global $gbdb;
			if(!empty($gbdb)) {
				$gbdb->createTables();
			}
		}
	}
	public function index() {
		$this->redirect(array("action" => "Translations"));
		exit;
	}
	public function Translations() {
		$this->set("page_title", __("Translations"));
	}
	public function License() {
		global $license;
		$this->set("page_title", __("License"));
		$domain = str_replace(array("http://", "https://"), "", Router::url('/', true));
		$this->set("domain", $domain);
		if ($this->request->is('post')) {
			if(empty($this->request->data['Configure']['license_email'])) {
				$this->Session->setFlash(__('Please enter License Email.'),'default', array('class' => 'note note-warning'));
				return;
			}

			if(empty($this->request->data['Configure']['license_key'])) {
				$this->Session->setFlash(__('Please enter License Key.'),'default', array('class' => 'note note-warning'));
				return;
			}
		
			$grassblade_license_check = grassblade_license_check($this->request->data['Configure']['license_email'], $this->request->data['Configure']['license_key'] );
			if($grassblade_license_check) {
			//	$return  = grassblade_save_license_file($this->request->data['Configure']['license_email'],$this->request->data['Configure']['license_key']);
				$this->Session->setFlash(__('License validated successfully. ').$grassblade_license_check,'default', array('class' => 'note note-success'));
			}
			else {
				grassblade_clear_license();
				$this->Session->setFlash(__('Please enter a valid license.'),'default', array('class' => 'note note-warning'));
			}
			return;
		}

		if(empty($license)) {
				$this->Session->setFlash(__('Please enter a valid license.'),'default', array('class' => 'note note-warning'));
		}
		else
				$this->Session->setFlash(__('Valid license found.'),'default', array('class' => 'note note-success'));


	}
	public function Backup() {
		$this->set("page_title", __("Backup"));

		if(isset($_REQUEST["remove"])) {
			if(!empty($_GET["save"])) {
				grassblade_config_set("backup_save", $_GET["save"]);
			}
			if(!empty($_GET["webdav"])) {
				grassblade_config_set("backup_webdav", $_GET["webdav"]);
			}
			if(!empty($_GET["mail"])) {
				grassblade_config_set("backup_mail", $_GET["mail"]);
			}
			if(!empty($_GET["ftp"])) {
				grassblade_config_set("backup_ftp", $_GET["ftp"]);
			}
			if(!empty($_GET["http"])) {
				grassblade_config_set("backup_http", $_GET["http"]);
			}
			include(APP."Vendor".DS."backup.php");
			exit;
		}
	}
	public function Integrations() {
		$this->set("page_title", __("Integrations"));
		if(!empty($_REQUEST["layout"]) && $_REQUEST["layout"] == "ajax_html") {
			if(!empty($_REQUEST["test"]) && $_REQUEST["test"] == "gb_xapi") {
				$wp = get_wordpress_client();
				if(is_object($wp))
				{
					echo __("Connected Successfully.");
					exit;
				}
				else
				{
					echo $wp;
					exit;
				}
			}
			exit;
		}
		if ($this->request->is('post')) {
			$Integrations = grassblade_config_set("Configure_Integrations", $this->request->data["Integrations"]);

			$this->Session->setFlash(__('The details have been saved. '), 'default', array('class' => 'note note-success'));
		}
		else
		{
			$this->request->data["Integrations"] = grassblade_config_get("Configure_Integrations");
		}

		$select = empty($_REQUEST["select"])? "gb_xapi":$_REQUEST["select"];
		$this->set("select", $select);
	}
	public function cron() {
		set_time_limit(3600); //2 hours
		if(User::get("role") != "admin")
			ini_set('display_errors', 0);

		$cron_id = microtime(true);
		grassblade_config_set("gb_cron_id", $cron_id);
		modified('gb_cron', "", $cron_id, $this);
		exit;
	}
}
