<?php

use Tests\WPGraphQL\TestCase\WPGraphQLTestCase;
use WPGraphQL\TEC\TEC;

/**
 * Class - TecTest
 */
class TecTest extends WPGraphQLTestCase {

	/**
	 * Run before each test.
	 */

	public function setUp() : void {
		// before...
		parent::setUp();

		// Your set up methods here.
	}

	/**
	 * Run after each test.
	 */
	public function tearDown() : void {
		// Your tear down methods here.

		// then ...
		parent::tearDown();
	}

	public function testInstance() : void {
		$actual = TEC::instance();

		$this->assertInstanceOf( TEC::class, $actual );
	}

	public function testIsTecLoaded() : void {
		$actual = TEC::is_tec_loaded();
		$this->assertTrue( $actual, 'Tec should be loaded' );
	}

	public function testIsEcpLoaded() : void {
		$actual = TEC::is_ecp_loaded();
		$this->assertTrue( $actual, 'ECP should be loaded' );

		// Test filter.
		tests_add_filter( 'tribe_events_calendar_pro_can_run', '__return_false' );
		$actual = TEC::is_ecp_loaded();
		$this->assertEquals( false, $actual, 'ECP should not be loaded' );
	}

	public function testIsEtLoaded() : void {
		$actual = TEC::is_et_loaded();
		$this->assertEquals( true, $actual, 'ET should be loaded' );
	}

	public function testIsEtpLoaded() : void {
		$actual = TEC::is_etp_loaded();
		$this->assertEquals( true, $actual, 'ETP should be loaded' );

		// Test filter.
		tests_add_filter( 'tribe_event_tickets_plus_can_run', '__return_false' );
		$actual = TEC::is_etp_loaded();
		$this->assertEquals( false, $actual, 'ETP should not loaded' );
	}
}
