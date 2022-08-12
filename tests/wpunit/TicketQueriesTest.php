<?php
class TicketQueriesTest extends \QL_Events\Test\TestCase\QLEventsTestCase {

    // tests
	public function testRSVPTicketQuery() {
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

		// Login as admin
		$this->loginAs( 1 );

		// Query ticket.
		$query     = '
			query($id: ID!) {
				rSVPTicket(id: $id idType: DATABASE_ID) {
					id
					databaseId
					title
				}
			}
		';
		$variables = array( 'id' => $ticket_id );
		$response  = $this->graphql( compact( 'query', 'variables' ) );

		// Assert response is correct.
		$expected = array(
			$this->expectedField( 'rSVPTicket.id', $this->toRelayId( 'post', $ticket_id ) ),
			$this->expectedField( 'rSVPTicket.databaseId', $ticket_id ),
			$this->expectedField( 'rSVPTicket.title', self::NOT_FALSY ),
		);
		$this->assertQuerySuccessful( $response, $expected );
	}

	public function testPayPalTicketQuery() {
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
		$ticket_id = $this->factory->ticket->create_paypal_ticket( $event_id );

		// Login as admin
		$this->loginAs( 1 );

		// Query ticket.
		$query     = '
			query($id: ID!) {
				payPalTicket(id: $id) {
					id
					databaseId
					title
				}
			}
		';
		$variables = array( 'id' => $this->toRelayId( 'post', $ticket_id ) );
		$response  = $this->graphql( compact( 'query', 'variables' ) );

		// Assert response is correct.
		$expected = array(
			$this->expectedField( 'payPalTicket.id', $this->toRelayId( 'post', $ticket_id ) ),
			$this->expectedField( 'payPalTicket.databaseId', $ticket_id ),
			$this->expectedField( 'payPalTicket.title', self::NOT_NULL ),
		);
		$this->assertQuerySuccessful( $response, $expected );
	}

	public function testTicketsQuery() {
		// Generate events.
		$event_one = $this->factory->event->create();
		$event_two = $this->factory->event->create();

		// Generate tickets.
		$rsvp_one   = $this->factory->ticket->create_rsvp_ticket( $event_one );
		$rsvp_two   = $this->factory->ticket->create_rsvp_ticket( $event_two );
		$paypal_one = $this->factory->ticket->create_paypal_ticket( $event_one );
		$paypal_two = $this->factory->ticket->create_paypal_ticket( $event_two );

		// Login as admin
		$this->loginAs( 1 );

		// Query tickets.
		$query = '
			query {
				tickets {
					nodes {
						... on RSVPTicket {
							id
							__typename
						}
						... on PayPalTicket {
							id
							__typename
						}
					}
				}
			}
		';
		$response = $this->graphql( compact( 'query' ) );

		// Assert response is correct.
		$expected = array(
			$this->expectedNode(
				'tickets.nodes',
				array(
					$this->expectedField( 'id', $this->toRelayId( 'post', $rsvp_one ) ),
					$this->expectedField( '__typename', 'RSVPTicket' ),
				)
			),
			$this->expectedNode(
				'tickets.nodes',
				array(
					$this->expectedField( 'id', $this->toRelayId( 'post', $rsvp_two ) ),
					$this->expectedField( '__typename', 'RSVPTicket' ),
				)
			),
			$this->expectedNode(
				'tickets.nodes',
				array(
					$this->expectedField( 'id', $this->toRelayId( 'post', $paypal_one ) ),
					$this->expectedField( '__typename', 'PayPalTicket' ),
				)
			),
			$this->expectedNode(
				'tickets.nodes',
				array(
					$this->expectedField( 'id', $this->toRelayId( 'post', $paypal_two ) ),
					$this->expectedField( '__typename', 'PayPalTicket' ),
				)
			),
		);
		$this->assertQuerySuccessful( $response, $expected );
	}

	public function testEventToTicketConnectionQuery() {
		// Generate events.
		$event_id      = $this->factory->event->create();

		// Generate tickets.
		$rsvp_id  = $this->factory->ticket->create_rsvp_ticket( $event_id );
		$paypal_id = $this->factory->ticket->create_paypal_ticket( $event_id );

		// Login as admin
		$this->loginAs( 1 );

		// Query tickets.
		$query     = '
			query($id: ID!) {
				event(id: $id) {
					id
					databaseId
					tickets {
						nodes {
							... on RSVPTicket {
								id
								__typename
							}
							... on PayPalTicket {
								id
								__typename
							}
						}
					}
				}
			}
		';
		$variables = array( 'id' => $this->toRelayId( 'post', $event_id ) );
		$response  = $this->graphql( compact( 'query', 'variables' ) );

		// Assert response is correct.
		$expected = array(
			$this->expectedField( 'event.id', $this->toRelayId( 'post', $event_id ) ),
			$this->expectedField( 'event.databaseId', $event_id ),
			$this->expectedNode(
				'event.tickets.nodes',
				array(
					$this->expectedField( 'id', $this->toRelayId( 'post', $rsvp_id ) ),
					$this->expectedField( '__typename', 'RSVPTicket' ),
				)
			),
			$this->expectedNode(
				'event.tickets.nodes',
				array(
					$this->expectedField( 'id', $this->toRelayId( 'post', $paypal_id ) ),
					$this->expectedField( '__typename', 'PayPalTicket' ),
				)
			),
		);
		$this->assertQuerySuccessful( $response, $expected );
	}

}
