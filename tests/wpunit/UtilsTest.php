<?php

use Tests\WPGraphQL\TestCase\WPGraphQLTestCase;
use WPGraphQL\TEC\Events\Type\WPObject\Event;
use WPGraphQL\TEC\TEC;
use WPGraphQL\TEC\Utils\Utils;

/**
 * Class - UtilsTest
 */
class UtilsTest extends WPGraphQLTestCase {

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

	public function testToCamelCase() : void {
		$expected = 'myGraphQLClass';

		$actual = Utils::to_camel_case( 'My-graphQ_l$class' );

		$this->assertEquals( $expected, $actual );
	}

	public function testEndsWith(): void {
		$expected = 'value';
		$actual = Utils::ends_with( 'my_long_value', $expected );

		$this->assertTrue( $actual );

		// Test empty.
		$actual = Utils::ends_with( 'my_long_value', '' );
		$this->assertTrue( $actual );
	}

	public function testStartsWith(): void {
		$expected = 'my';
		$actual = Utils::starts_with( 'my_long_value', $expected );

		$this->assertTrue( $actual );
	}

	public function testGetEnabledPostTypesForTickets() : void {
		$actual = Utils::get_enabled_post_types_for_tickets();

		$this->assertContains( 'page', $actual, 'Page should be a valid type' );
		$this->assertContains( 'tribe_events', $actual, 'Events should be a valid type' );
	}

	public function testIsTecPostType() : void {
		$actual = Utils::is_tec_post_type( 'tribe_organizer');

		$this->assertTrue( $actual, 'Organizer should be a valid post type' );

		$actual = Utils::is_tec_post_type( 'post' );
		$this->assertFalse( $actual, 'Post should not be a valid post type' );
	}

	public function testGetEtProviderForType() : void {
		// test cpt name.
		$expected = 'rsvp';
		$actual = Utils::get_et_provider_for_type( 'tribe_rsvp_tickets' );

		$this->assertEquals( $expected, $actual, 'The provider for `tribe_rsvp_tickets` should be `rsvp`.' );

		// test graphql name.
		$expected = 'tribe-commerce';
		$actual = Utils::get_et_provider_for_type( 'PayPalTicket' );

		$this->assertEquals( $expected, $actual, 'The provider for `PayPalTicket` should be `tribe-commerce`.' );

		// test tickets-commerce.
		$expected = 'tickets-commerce';
		$actual = Utils::get_et_provider_for_type( 'TcAttendee' );

		$this->assertEquals( $expected, $actual, 'The provider for `TcAttendee` should be `tickets-commerce`.' );

		// Test bad value.
		$expected = 'default';
		$actual = Utils::get_et_provider_for_type( 'Not_a_realType' );
		$this->assertEquals( $expected, $actual, 'The default provider should be `default`.' );
	}

	public function testGetEtOrderTypes() : void {
		$actual = Utils::get_et_order_types();

		$expected = [
			'tec_tc_order'     => 'TcOrder',
			'tribe_tpp_orders' => 'PayPalOrder',
		];

		$this->assertEquals( $expected, $actual );
	}

	public function testGetEtAttendeeTypes() : void {
		$actual = Utils::get_et_attendee_types();

		$expected = [
			'tribe_rsvp_attendees' => 'RsvpAttendee',
			'tribe_tpp_attendees'  => 'PayPalAttendee',
			'tec_tc_attendee'      => 'TcAttendee',
		];

		$this->assertEquals( $expected, $actual );
	}

	public function testGetEtTicketTypes() : void {
		$actual = Utils::get_et_ticket_types();

		$expected = [
			'tribe_rsvp_tickets' => 'RsvpTicket',
			'tec_tc_ticket'      => 'TcTicket',
			'tribe_tpp_tickets'  => 'PayPalTicket',
		];

		$this->assertEquals( $expected, $actual );
	}

	public function testGetEtTypes() : void {
		$actual = Utils::get_et_types();

		$expected = [
			'tribe_rsvp_tickets' => 'RsvpTicket',
			'tec_tc_ticket'      => 'TcTicket',
			'tribe_tpp_tickets'  => 'PayPalTicket',
			'tribe_rsvp_attendees' => 'RsvpAttendee',
			'tribe_tpp_attendees'  => 'PayPalAttendee',
			'tec_tc_attendee'      => 'TcAttendee',
			'tec_tc_order'     => 'TcOrder',
			'tribe_tpp_orders' => 'PayPalOrder',
		];

		$this->assertEquals( $expected, $actual );
	}

	public function testGetRegisteredTaxonomies() : void {
		$actual = Utils::get_registered_taxonomies();
		$expected = [ 'tribe_events_cat' => 'EventCategory' ];

		$this->assertEquals( $expected, $actual, 'EventCategory should be a registered taxonomy.' );

		// Test filter.
		$expected = [ 'some_other_tax' => 'SomeOtherTax' ];
		add_filter( 'grapqhl_tec_taxonomy_types', fn( $types ) => $expected, 10 );

		$actual = Utils::get_registered_taxonomies();
		$this->assertEquals( $expected, $actual, '`graphql_tec_taxonomy_types` is not overwriting registered taxonomies.' );
	}

	public function testGetRegisteredPostTypes() : void {
		// Test filter.
		$expected = [ 'some_other_post_type' => 'some_other_post_type' ];
		add_filter( 'graphql_tec_post_types', fn( $types ) => $expected, 10 );

		$actual = Utils::get_registered_post_types();
		$this->assertEquals( $expected, $actual, '`graphql_tec_post_types` is not overwriting registered post types.' );
	}

	public function testGraphqlTypeToPostType() : void {
		$expected = Event::$wp_type;

		$actual = Utils::graphql_type_to_post_type( Event::$type );

		$this->assertEquals( $expected, $actual );
	}

	public function testPostTypeToGraphqlType() : void {
		$expected = Event::$type;

		$actual = Utils::post_type_to_graphql_type( Event::$wp_type );

		$this->assertEquals( $actual, $expected );
	}
}
