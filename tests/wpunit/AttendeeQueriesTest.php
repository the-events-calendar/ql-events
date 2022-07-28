<?php

class AttendeeQueriesTest extends \QL_Events\Test\TestCase\QLEventsTestCase {

    // TODO: add test.
    public function testAttendeeConnectionQueries() {
		// Generate organizers.
		$organizer_one = $this->factory->organizer->create();
		$organizer_two = $this->factory->organizer->create();
		// Generate venue/event.
		$venue_id      = $this->factory->venue->create();
		$event_id      = $this->factory->event->create(
			array(
				'venue' => $venue_id,
				'organizers' => array( $organizer_one, $organizer_two ),
			)
		);

		// Generate ticket.
		$ticket_id = $this->factory->ticket->create_rsvp_ticket( $event_id );

		// Generate attendee.
		$attendee_id = $this->factory->rsvp_attendee->create_attendee_for_ticket( $ticket_id, $event_id );
		$full_name   = get_post_meta( $attendee_id, tribe( 'tickets.rsvp' )->full_name, true );

		// Query for event attendees
		$query     = '
			query($id: ID!) {
				event(id: $id idType: DATABASE_ID) {
					attendees {
						nodes {
							id
							databaseId
							fullName
						}
					}
				}
			}
		';
		$variables = array( 'id' => $event_id );
		$response  = $this->graphql( compact( 'query', 'variables' ) );

		// Expect empty results because guest user querying.
		$expected = array(
			$this->expectedField( 'event.attendees.nodes', array() )
		);
		$this->assertQuerySuccessful( $response, $expected );


		// Query again as admin.
		$this->loginAs( 1 );
		$response = $this->graphql( compact( 'query', 'variables' ) );

		// Confirm attendee in results.
		$expected = array(
			$this->expectedNode(
				'event.attendees.nodes',
				array(
					'id'         => $this->toRelayId( 'post', $attendee_id ),
					'databaseId' => $attendee_id,
					'fullName'   => $full_name
				)
			)
		);
		$this->assertQuerySuccessful( $response, $expected );
    }

}
