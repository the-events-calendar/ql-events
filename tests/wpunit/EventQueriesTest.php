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

	public function testEventQueryWithEventsVirtualFields() {
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
					isVirtual
					virtualUrl
					virtualEmbedVideo
					virtualLinkedButton
					virtualLinkedButtonText
					virtualShowEmbedAt
					virtualShowEmbedTo
					virtualShowLeadUp
					virtualIsLinkable
					virtualIsImmediate
					virtualShouldShowEmbed
					virtualShouldShowLink
                }
            }
        ';

		/**
		 * Assertion One
		 *
		 * Test response of a non-virtual event.
		 */
        $variables = array(
			'id' => $this->toRelayId( 'post', $event_id ),
		);
		$response  = $this->graphql( compact( 'query', 'variables' ) );
		$expected  = [
			$this->expectedField( 'event.id', $this->toRelayId( 'post', $event_id ) ),
			$this->expectedField( 'event.isVirtual', self::IS_FALSY ),
			$this->expectedField( 'event.virtualUrl', self::IS_NULL ),
			$this->expectedField( 'event.virtualEmbedVideo', true ),
			$this->expectedField( 'event.virtualLinkedButton', true ),
			$this->expectedField( 'event.virtualLinkedButtonText', 'Watch' ),
			$this->expectedField( 'event.virtualShowEmbedAt', 'ON_EVENT_PUBLISH' ),
			$this->expectedField( 'event.virtualShowEmbedTo', [ 'EVERYONE' ] ),
			$this->expectedField( 'event.virtualShowLeadUp', 15.0 ),
			$this->expectedField( 'event.virtualIsLinkable', false ),
			$this->expectedField( 'event.virtualIsImmediate', false ),
			$this->expectedField( 'event.virtualShouldShowEmbed', false ),
			$this->expectedField( 'event.virtualShouldShowLink', false ),
		];

		$this->assertQuerySuccessful( $response, $expected );
	}
}
