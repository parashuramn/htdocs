<?php
App::uses('AppModel', 'Model');
/**
 * GbContent Model
 *
 * @property GbContentType $GbContentType
 * @property GbContent $ParentGbContent
 * @property GbContent $ChildGbContent
 */
class Content extends AppModel {

	public $useTable = "gb_contents";

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'name';


	// The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * hasOne associations
 *
 * @var array
 */
	public $belongsTo = array(
		'ContentType' => array(
			'className' => 'ContentType',
			'foreignKey' => 'type',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
}
