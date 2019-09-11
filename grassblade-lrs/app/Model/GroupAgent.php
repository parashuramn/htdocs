<?php
App::uses('AppModel', 'Model');
/**
 * Group Model
 *
 * @property Program $Program
 */
class GroupAgent extends AppModel {

	public $useTable = 'gb_group_agents';


/**
 * Display field
 *
 * @var string
 */

/**
 * Validation rules
 *
 * @var array
 */

	public $belongsTo = "Group";
	//public $hasMany = array("Statement" => array('foreignKey' => 'agent_id'));

}
