<?php
App::uses('GbActivitiesState', 'Model');

/**
 * GbActivitiesState Test Case
 *
 */
class GbActivitiesStateTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.gb_activities_state'
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->GbActivitiesState = ClassRegistry::init('GbActivitiesState');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->GbActivitiesState);

		parent::tearDown();
	}

}
