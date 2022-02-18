<?php
class TicketQueriesTest extends \QL_Events\Test\TestCase\QLEventsTestCase {

    // tests
    public function testTicketQuery() {
        $organizer_one = $this->factory->organizer->create();
        $organizer_two = $this->factory->organizer->create();
        $venue_id      = $this->factory->venue->create();
        $event_id      = $this->factory->event->create(
            array(
                'venue' => $venue_id,
                'organizers' => array( $organizer_one, $organizer_two ),
            )
        );
    }

}
