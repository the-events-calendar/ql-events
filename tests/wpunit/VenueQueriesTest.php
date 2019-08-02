<?php

use QL_Events\Test\Factories\Venue;

class VenueQueriesTest extends \Codeception\TestCase\WPTestCase
{

    public function setUp() {
        // before
        parent::setUp();

        $this->admin            = $this->factory->user->create( array( 'role' => 'admin' ) );
		$this->customer         = $this->factory->user->create( array( 'role' => 'customer' ) );
        $this->helper           = $this->getModule('\Helper\Wpunit')->venue();
        $this->factory()->venue = new Venue();
    }

    public function tearDown() {
        // your tear down methods here

        // then
        parent::tearDown();
    }

    // tests
    public function testVenueQuery() {
        $venue_id = $this->factory()->venue->create();

        // Create test query
        $query = '
            query($id: ID!) {
                venue(id: $id) {
                    country
                    address
                    city
                    stateProvince
                    state
                    province
                    zip
                    phone
                    url
                    showMap
                    showMapLink
                }
            }
        ';

        /**
		 * Assertion One
		 */
        $variables = array( 'id' => $this->helper->to_relay_id( $venue_id ) );
		$actual    = graphql(
            array(
                'query'     => $query,
                'variables' => $variables,
            )
        );
		$expected  = array(
            'data' => array(
                'venue' => $this->helper->print_query( $venue_id ),
            ),
        );

		// use --debug flag to view.
		codecept_debug( $actual );

		$this->assertEqualSets( $expected, $actual );
    }

}