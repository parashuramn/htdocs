<?php
App::uses('AppModel', 'Model');
/**
 * User Model
 *
 * @property Program $Program
 */
class User extends AppModel {

	public $useTable = 'gb_users';


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
		'password' => array(
		),
		'email' => array(
			'email' => array(
				'rule' => array('email'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
	);

	//The Associations below have been created with all possible keys, those that are not needed can be removed

	public $hasMany = array("UsersAuth" => array('foreignKey' => 'user_id'));
	
	public function beforeSave($options = array()) {
        parent::beforeSave();
		if(!empty($this->data['User']['password']))
        $this->data['User']['password'] = md5($this->data['User']['password']);
		else
		unset($this->data['User']['password']);
        return true;
    }

	static function &getInstance($user=null) {
	  static $instance = array();
		
	  if ($user) {
		$instance[0] =& $user;
	  }

	  if (empty($instance[0])) {
		trigger_error(__("User not set.", true), E_USER_WARNING);
		$user = null;
		$instance[0] =& $user;
	  }

	  return $instance[0];
	}

	static function store($user) {

	  if (empty($user)) {
		return false;
	  }

	  User::getInstance($user);
	}

	static function get($path) {
	  $_user =& User::getInstance();

	  $path = str_replace('.', '/', $path);
	  if (strpos($path, 'User') !== 0) {
		$path = sprintf('User/%s', $path);
	  }

	  if (strpos($path, '/') !== 0) {
		$path = sprintf('/%s', $path);
	  }

	  $value = Set::extract($path, $_user);

	  if (!$value) {
		return false;
	  }

	  return $value[0];
	}	
}
