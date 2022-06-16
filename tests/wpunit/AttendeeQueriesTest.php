<?php

class AttendeeQueriesTest extends \QL_Events\Test\TestCase\QLEventsTestCase {

    // TODO: add test.
    public function testAttendeeConnectionQueries() {
		// Generate event.
		$organizer_one = $this->factory->organizer->create();
		$organizer_two = $this->factory->organizer->create();
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
    }

}
