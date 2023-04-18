<?php

class EventQueriesTest extends \QL_Events\Test\TestCase\QLEventsTestCase {
    public function expectedEventData( $id ) {
		$event = tribe_get_event( $id );

		$expected = [
			$this->expectedField( 'event.id', $this->toRelayId( 'post', $id ) ),
			$this->expectedField( 'event.databaseId', $event->ID ),
			$this->expectedField( 'event.allDay', $event->all_day ),
			$this->expectedField( 'event.startDate', $event->start_date ),
			$this->expectedField( 'event.endDate', $event->end_date ),
		];

		foreach ( $event->organizers as $organizer ) {
			$expected[] = $this->expectedNode(
				'event.organizers.nodes',
				array(
					'id'         => $this->toRelayId( 'post', $organizer->ID ),
					'databaseId' => $organizer->ID
				)
			);
		}

		foreach ( $event->venues as $venue ) {
			$expected[] = $this->expectedField(
				'event.venue',
				array(
					'id'         => $this->toRelayId( 'post', $venue->ID ),
					'databaseId' => $venue->ID
				)
			);
		}

		return $expected;
	}

	// tests
    public function testEventQueries() {
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
					id
					databaseId
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

	public function testEventsQueryAndDateFilters() {
		// Create organizer and venue.
		$organizer = $this->factory->organizer->create();
        $venue_id  = $this->factory->venue->create();

		// Create events.
        $tomorrow  = $this->factory->event->create(
            array(
				'when'       => strtotime( '+1 day' ),
                'venue'      => $venue_id,
                'organizers' => array( $organizer,),
            )
        );
		$yesterday = $this->factory->event->create(
            array(
				'when'       => strtotime( '-1 day' ),
                'venue'      => $venue_id,
                'organizers' => array( $organizer,),
            )
        );
		$next_week = $this->factory->event->create(
            array(
				'when'       => strtotime( '+1 week' ),
                'venue'      => $venue_id,
                'organizers' => array( $organizer,),
            )
        );
		$last_week = $this->factory->event->create(
            array(
				'when'       => strtotime( '-1 week' ),
                'venue'      => $venue_id,
                'organizers' => array( $organizer,),
            )
        );


		$query =  '
			query ($where: RootQueryToEventConnectionWhereArgs) {
				events(first: 20 where: $where) {
					nodes { id }
				}
			}
		';

		/**
		 * Asserts only events that hours 18 ago or later
		 * are return using the "startsAfter" filter.
		 */
		$variables = [ 'where' => [ 'startsAfter' => '-18 hours' ] ];
		$response = $this->graphql( compact( 'query', 'variables' ) );
		$expected = [
			$this->expectedField( 'events.nodes.#.id', $this->toRelayId( 'post', $tomorrow ) ),
			$this->expectedField( 'events.nodes.#.id', $this->toRelayId( 'post', $next_week ) ),
			$this->not()->expectedField( 'events.nodes.#.id', $this->toRelayId( 'post', $yesterday ) ),
			$this->not()->expectedField( 'events.nodes.#.id', $this->toRelayId( 'post', $next_week ) ),
		];

		$this->assertQuerySuccessful( $response, $expected );

	}
}
