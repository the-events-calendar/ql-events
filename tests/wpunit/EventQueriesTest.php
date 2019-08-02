<?php

use QL_Events\Test\Factories\Event;
use QL_Events\Test\Factories\Venue;
use QL_Events\Test\Factories\Organizer;

class EventQueriesTest extends \Codeception\TestCase\WPTestCase {
    private $admin;
    private $customer;
    private $helper;

    public function setUp() {
        // before
        parent::setUp();

        $this->admin                = $this->factory->user->create( array( 'role' => 'admin' ) );
		$this->customer             = $this->factory->user->create( array( 'role' => 'customer' ) );
        $this->helper               = $this->getModule('\Helper\Wpunit')->event();
        $this->factory()->event     = new Event();
        $this->factory()->venue     = new Venue();
        $this->factory()->organizer = new Organizer();
    }

    public function tearDown() {
        // your tear down methods here

        // then
        parent::tearDown();
    }

    // tests
    public function testEventsQueries() {
        $organizer_one = $this->factory()->organizer->create();
        $organizer_two = $this->factory()->organizer->create();
        $venue_id     = $this->factory()->venue->create();
        $event_id     = $this->factory()->event->create(
            array(
                'venue' => $venue_id,
                'organizers' => array( $organizer_one, $organizer_two ),
            )
        );

        // Create test query
        $query = '
            query($id: ID!) {
                event(id: $id) {
                    allDay
                    startDate
                    endDate
                    duration
                    showMapLink
                    showMap
                    currencySymbol
                    currencyPosition
                    cost
                    costMin
                    costMax
                    url
                    phone
                    hideFromUpcoming
                    timezone
                    timezoneAbbr
                    origin
                    featured
                    venue {
                        id
                    }
                    organizers {
                        nodes {
                            id
                        }
                    }
                }
            }
        ';

        /**
		 * Assertion One
		 */
        $variables = array( 'id' => $this->helper->to_relay_id( $event_id ) );
		$actual    = graphql(
            array(
                'query'     => $query,
                'variables' => $variables,
            )
        );
		$expected  = array(
            'data' => array(
                'event' => $this->helper->print_query( $event_id ),
            ),
        );

		// use --debug flag to view.
		codecept_debug( $actual );

		$this->assertEqualSets( $expected, $actual );
    }

}