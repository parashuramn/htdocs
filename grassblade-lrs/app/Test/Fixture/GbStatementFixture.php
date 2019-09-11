<?php
/**
 * GbStatementFixture
 *
 */
class GbStatementFixture extends CakeTestFixture {

/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'primary'),
		'statement_id' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 128, 'key' => 'unique', 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'agent_name' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 1024, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'agent_mbox' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 1024, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'user_id' => array('type' => 'integer', 'null' => false, 'default' => '0'),
		'version' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 11, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'verb_id' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 2048, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'verb' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 2048, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'objectid' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 2048, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'object_objectType' => array('type' => 'string', 'null' => false, 'default' => 'Activity', 'length' => 1024, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'object_definition_type' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 2048, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'object_definition_name' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 2048, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'object_definition_description' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 2048, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'stored' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 1024, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'timestamp' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 1024, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'result_score_raw' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 1024, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'result_score_scaled' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 11, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'result_score_min' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 1024, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'result_score_max' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 1024, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'result_completion' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 11, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'result_success' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 11, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'result_duration' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 128, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'parent_ids' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'grouping_ids' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'statement' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'statement_id' => array('column' => 'statement_id', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_unicode_ci', 'engine' => 'InnoDB')
	);

/**
 * Records
 *
 * @var array
 */
	public $records = array(
		array(
			'id' => 1,
			'statement_id' => 'Lorem ipsum dolor sit amet',
			'agent_name' => 'Lorem ipsum dolor sit amet',
			'agent_mbox' => 'Lorem ipsum dolor sit amet',
			'user_id' => 1,
			'version' => 'Lorem ips',
			'verb_id' => 'Lorem ipsum dolor sit amet',
			'verb' => 'Lorem ipsum dolor sit amet',
			'objectid' => 'Lorem ipsum dolor sit amet',
			'object_objectType' => 'Lorem ipsum dolor sit amet',
			'object_definition_type' => 'Lorem ipsum dolor sit amet',
			'object_definition_name' => 'Lorem ipsum dolor sit amet',
			'object_definition_description' => 'Lorem ipsum dolor sit amet',
			'stored' => 'Lorem ipsum dolor sit amet',
			'timestamp' => 'Lorem ipsum dolor sit amet',
			'result_score_raw' => 'Lorem ipsum dolor sit amet',
			'result_score_scaled' => 'Lorem ips',
			'result_score_min' => 'Lorem ipsum dolor sit amet',
			'result_score_max' => 'Lorem ipsum dolor sit amet',
			'result_completion' => 'Lorem ips',
			'result_success' => 'Lorem ips',
			'result_duration' => 'Lorem ipsum dolor sit amet',
			'parent_ids' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
			'grouping_ids' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
			'statement' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.'
		),
	);

}
