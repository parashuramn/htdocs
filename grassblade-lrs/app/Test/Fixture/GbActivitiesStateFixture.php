<?php
/**
 * GbActivitiesStateFixture
 *
 */
class GbActivitiesStateFixture extends CakeTestFixture {

/**
 * Table name
 *
 * @var string
 */
	public $table = 'gb_activities_state';

/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'primary'),
		'stateId' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 2048, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'agent' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'registration' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 64, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'activityId' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 2048, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'content_type' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 128, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'agent_name' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 1024, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'agent_mbox' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 1024, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'agent_mbox_sha1sum' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 64, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'agent_openid' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 1024, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'agent_account_homePage' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 1024, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'agent_account_name' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 1024, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'created' => array('type' => 'timestamp', 'null' => false, 'default' => 'CURRENT_TIMESTAMP'),
		'modified' => array('type' => 'timestamp', 'null' => false, 'default' => '0000-00-00 00:00:00'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'InnoDB')
	);

/**
 * Records
 *
 * @var array
 */
	public $records = array(
		array(
			'id' => 1,
			'stateId' => 'Lorem ipsum dolor sit amet',
			'agent' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
			'registration' => 'Lorem ipsum dolor sit amet',
			'activityId' => 'Lorem ipsum dolor sit amet',
			'content_type' => 'Lorem ipsum dolor sit amet',
			'agent_name' => 'Lorem ipsum dolor sit amet',
			'agent_mbox' => 'Lorem ipsum dolor sit amet',
			'agent_mbox_sha1sum' => 'Lorem ipsum dolor sit amet',
			'agent_openid' => 'Lorem ipsum dolor sit amet',
			'agent_account_homePage' => 'Lorem ipsum dolor sit amet',
			'agent_account_name' => 'Lorem ipsum dolor sit amet',
			'created' => 1387458427,
			'modified' => 1387458427
		),
	);

}
