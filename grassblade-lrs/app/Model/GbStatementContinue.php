<?php
App::uses('AppModel', 'Model');
require_once(APP."Vendor".DS."input.php");
require_once(APP."Vendor".DS."output.php");

/**
 * StatementContinue Model
 *
 */
class GbStatementContinue extends AppModel {
	public $useTable		= 'gb_statements_continue';

/**
 * Display field
 *
 * @var string
 */
	public $displayField 	= 'continueToken';
	
}
