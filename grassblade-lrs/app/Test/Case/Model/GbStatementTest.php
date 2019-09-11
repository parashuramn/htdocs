<?php
App::uses('GbStatement', 'Model');

/**
 * GbStatement Test Case
 *
 */
class GbStatementTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.gb_statement'
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->GbStatement = ClassRegistry::init('GbStatement');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->GbStatement);

		parent::tearDown();
	}

}
