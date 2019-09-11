<?php
App::uses('AppModel', 'Model');
/**
 * User Model
 *
 * @property Program $Program
 */
class UsersAuth extends AppModel {

	public $useTable = 'gb_users_auth';


/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'api_user';
	
}
