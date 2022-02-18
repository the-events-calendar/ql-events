<?php

class EventQueriesTest extends \QL_Events\Test\TestCase\QLEventsTestCase {
    public function expectedEventData( $id ) {
		$event = tribe_get_event( $id );

		$expected = array(
			$this->expectedField( 'event.id', $this->toRelayId( 'post', $id ) ),
			$this->expectedField( 'event.databaseId', $event->ID ),
			$this->expectedField( 'event.allDay', $event->all_day ),
			$this->expectedField( 'event.startDate', $event->start_date ),
			$this->expectedField( 'event.endDate', $event->end_date ),
			$this->expectedField( 'event.duration', $event->duration ),
			$this->expectedField( 'event.cost', $event->cost ),
		);

		foreach ( $event->organizers as $organizer ) {
			$expected[] = $this->expectedNode( 'event.organizers.nodes', array( 'databaseId' => $organizer->ID ) );
		}

		foreach ( $event->venues as $venue ) {
			$expected[] = $this->expectedNode( 'event.venues.nodes', array( 'databaseId' => $venue->ID ) );
		}

		return $expected;
	}

	// tests
    public function testEventsQueries() {
        $organizer_one = $this->factory->organizer->create();
        $organizer_two = $this->factory->organizer->create();
        $venue_id      = $this->factory->venue->create();
        $event_id      = $this->factory->event->create(
            array(
                'venue'      => $venue_id,
                'organizers' => array( $organizer_one, $organizer_two ),
            )
        );

        // Create test query.
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
						databaseId
                    }
                    organizers {
                        nodes {
                            id
							databaseId
                        }
                    }
                }
            }
        ';

        /**
		 * Assertion One
		 *
		 * Assert "Event" field types and values.
		 */
        $variables = array(
			'id' => $this->toRelayId( 'post', $event_id ),
		);
		$response  = $this->graphql( compact( 'query', 'variables' ) );
		$expected  = $this->expectedEventData( $event_id );

		$this->assertQuerySuccessful( $response, $expected );
    }
}
