<?php
App::uses('AppModel', 'Model');
/**
 * GbContentType Model
 *
 */
class ContentType extends AppModel {

	public $useTable = "gb_content_types";

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'name';

}
