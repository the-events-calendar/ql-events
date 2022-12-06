<?php

class AttendeeMutationsTest extends \QL_Events\Test\TestCase\QLEventsTestCase {
	/**
	 * {@inheritdoc}
	 */
	public function setUp(): void {
		parent::setUp();

		// Enable Tribe Commerce.
		add_filter( 'tribe_tickets_commerce_paypal_is_active', '__return_true' );
		add_filter( 'tribe_tickets_get_modules', function ( $modules ) {
			$modules['Tribe__Tickets__Commerce__PayPal__Main'] = tribe( 'tickets.commerce.paypal' )->plugin_name;

			return $modules;
		} );
	}

	public function testAttendeeMutations() {
		// Authenticate as admin, because attendee is private.
		$this->loginAs(1);

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
		$ticket_id = $this->factory->ticket->create_paypal_ticket( $event_id );

		/**
		 * Assertion 1
		 *
		 * Test "registerAttendee" mutation response.
		 */
		$query = '
			mutation($input: RegisterAttendeeInput!) {
				registerAttendee(input: $input) {
					attendee {
						id
						databaseId
						fullName
						email
					}
				}
			}
		';
		$variables = array(
			'input' => array(
				'ticketId'         => $ticket_id,
				'eventId'          => $event_id,
				'name'             => 'Bob Dole',
				'email'            => 'bob@dole.com',
				'additionalFields' => array(
					array(
						'key'   => 'optout',
						'value' => 'yes'
					),
					array(
						'key'   => 'order_status',
						'value' => 'yes'
					)
				)
			)
		);
		$response = $this->graphql( compact( 'query', 'variables' ) );
		$expected = array(
			$this->expectedField( 'registerAttendee.attendee', self::NOT_NULL ),
			$this->expectedField( 'registerAttendee.attendee.id', self::NOT_NULL ),
			$this->expectedField( 'registerAttendee.attendee.databaseId', self::NOT_NULL ),
			$this->expectedField( 'registerAttendee.attendee.fullName', 'Bob Dole' ),
			$this->expectedField( 'registerAttendee.attendee.email', 'bob@dole.com' ),
		);

		$this->assertQuerySuccessful( $response, $expected );



		/**
		 * Assertion 2
		 *
		 * Test "updateAttendee" mutation response.
		 */
		$query = '
			mutation($input: UpdateAttendeeInput!) {
				updateAttendee(input: $input) {
					attendee {
						id
						databaseId
						fullName
						email
					}
				}
			}
		';

		$attendee_db_id = self::lodashGet( $response, 'data.registerAttendee.attendee.databaseId' );
		$attendee_id = self::lodashGet( $response, 'data.registerAttendee.attendee.id' );
		$variables = array(
			'input' => array(
				'attendeeId'       => $attendee_id,
				'name'             => 'Dave Dole',
				'email'            => 'dave@dole.com',
			)
		);

		$response = $this->graphql( compact( 'query', 'variables' ) );
		$expected = array(
			$this->expectedField( 'updateAttendee.attendee', self::NOT_NULL ),
			$this->expectedField( 'updateAttendee.attendee.id', $attendee_id ),
			$this->expectedField( 'updateAttendee.attendee.databaseId', $attendee_db_id ),
			$this->expectedField( 'updateAttendee.attendee.fullName', 'Dave Dole' ),
			$this->expectedField( 'updateAttendee.attendee.email', 'dave@dole.com' ),
		);

		$this->assertQuerySuccessful( $response, $expected );

	}
}
