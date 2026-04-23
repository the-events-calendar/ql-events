<?php

class AttendeeMutationsTest extends \QL_Events\Test\TestCase\QLEventsTestCase {
	/**
	 * {@inheritdoc}
	 */
	public function setUp(): void {
		parent::setUp();

		// Enable Tribe Commerce.
		add_filter( 'tribe_tickets_commerce_paypal_is_active', '__return_true' );
		add_filter(
			'tribe_tickets_get_modules',
			function ( $modules ) {
				$modules['Tribe__Tickets__Commerce__PayPal__Main'] = tribe( 'tickets.commerce.paypal' )->plugin_name;

				return $modules;
			}
		);
	}

	public function testAttendeeMutations() {
		// Authenticate as admin, because attendee is private.
		$this->loginAs( 1 );

		// Generate organizers.
		$organizer_one = $this->factory->organizer->create();
		$organizer_two = $this->factory->organizer->create();
		// Generate venue/event.
		$venue_id = $this->factory->venue->create();
		$event_id = $this->factory->event->create(
			[
				'venue'      => $venue_id,
				'organizers' => [ $organizer_one, $organizer_two ],
			]
		);

		// Generate ticket.
		$ticket_id = $this->factory->ticket->create_paypal_ticket( $event_id );

		/**
		 * Assertion 1
		 *
		 * Test "registerAttendee" mutation response.
		 */
		$query     = '
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
		$variables = [
			'input' => [
				'ticketId'         => $ticket_id,
				'eventId'          => $event_id,
				'name'             => 'Bob Dole',
				'email'            => 'bob@dole.com',
				'additionalFields' => [
					[
						'key'   => 'optout',
						'value' => 'yes',
					],
					[
						'key'   => 'order_status',
						'value' => 'yes',
					],
				],
			],
		];
		$response  = $this->graphql( compact( 'query', 'variables' ) );
		$expected  = [
			$this->expectedField( 'registerAttendee.attendee', self::NOT_NULL ),
			$this->expectedField( 'registerAttendee.attendee.id', self::NOT_NULL ),
			$this->expectedField( 'registerAttendee.attendee.databaseId', self::NOT_NULL ),
			$this->expectedField( 'registerAttendee.attendee.fullName', 'Bob Dole' ),
			$this->expectedField( 'registerAttendee.attendee.email', 'bob@dole.com' ),
		];

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
		$attendee_id    = self::lodashGet( $response, 'data.registerAttendee.attendee.id' );
		$variables      = [
			'input' => [
				'attendeeId' => $attendee_id,
				'name'       => 'Dave Dole',
				'email'      => 'dave@dole.com',
			],
		];

		$response = $this->graphql( compact( 'query', 'variables' ) );
		$expected = [
			$this->expectedField( 'updateAttendee.attendee', self::NOT_NULL ),
			$this->expectedField( 'updateAttendee.attendee.id', $attendee_id ),
			$this->expectedField( 'updateAttendee.attendee.databaseId', $attendee_db_id ),
			$this->expectedField( 'updateAttendee.attendee.fullName', 'Dave Dole' ),
			$this->expectedField( 'updateAttendee.attendee.email', 'dave@dole.com' ),
		];

		$this->assertQuerySuccessful( $response, $expected );
	}

	/**
	 * Seeds a published event with an RSVP ticket and returns their IDs.
	 *
	 * @return array{event_id:int,ticket_id:int}
	 */
	private function seed_event_and_ticket(): array {
		$venue_id  = $this->factory->venue->create();
		$event_id  = $this->factory->event->create( [ 'venue' => $venue_id ] );
		$ticket_id = $this->factory->ticket->create_paypal_ticket( $event_id );

		return [
			'event_id'  => $event_id,
			'ticket_id' => $ticket_id,
		];
	}

	private function register_attendee_mutation(): string {
		return '
			mutation($input: RegisterAttendeeInput!) {
				registerAttendee(input: $input) {
					attendee { id databaseId fullName email }
				}
			}
		';
	}

	private function update_attendee_mutation(): string {
		return '
			mutation($input: UpdateAttendeeInput!) {
				updateAttendee(input: $input) {
					attendee { id databaseId fullName email }
				}
			}
		';
	}

	/**
	 * An anonymous caller (no user context) must not be able to create attendees.
	 */
	public function testRegisterAttendeeRejectsUnauthenticatedRequests() {
		$this->logout();

		[ 'event_id' => $event_id, 'ticket_id' => $ticket_id ] = $this->seed_event_and_ticket();

		$response = $this->graphql(
			[
				'query'     => $this->register_attendee_mutation(),
				'variables' => [
					'input' => [
						'ticketId' => $ticket_id,
						'eventId'  => $event_id,
						'name'     => 'Anon Attacker',
						'email'    => 'attacker@proof-of-concept.test',
					],
				],
			]
		);

		$this->assertNotEmpty( $response['errors'] ?? null, 'Unauthenticated registerAttendee must return a GraphQL error.' );
		$this->assertStringContainsString( 'permission', strtolower( $response['errors'][0]['message'] ?? '' ) );
	}

	/**
	 * An authenticated user without `edit_post` on the event must not be able to create attendees.
	 */
	public function testRegisterAttendeeRejectsUsersWithoutEditCapability() {
		$subscriber_id = $this->factory()->user->create( [ 'role' => 'subscriber' ] );
		$this->loginAs( $subscriber_id );

		[ 'event_id' => $event_id, 'ticket_id' => $ticket_id ] = $this->seed_event_and_ticket();

		$response = $this->graphql(
			[
				'query'     => $this->register_attendee_mutation(),
				'variables' => [
					'input' => [
						'ticketId' => $ticket_id,
						'eventId'  => $event_id,
						'name'     => 'Subscriber Attempt',
						'email'    => 'sub@proof-of-concept.test',
					],
				],
			]
		);

		$this->assertNotEmpty( $response['errors'] ?? null, 'A subscriber must not be able to register attendees.' );
		$this->assertStringContainsString( 'permission', strtolower( $response['errors'][0]['message'] ?? '' ) );
	}

	/**
	 * An anonymous caller must not be able to modify an existing attendee.
	 */
	public function testUpdateAttendeeRejectsUnauthenticatedRequests() {
		// Seed an attendee as admin so we have a target.
		$this->loginAs( 1 );
		[ 'event_id' => $event_id, 'ticket_id' => $ticket_id ] = $this->seed_event_and_ticket();

		$create_response = $this->graphql(
			[
				'query'     => $this->register_attendee_mutation(),
				'variables' => [
					'input' => [
						'ticketId' => $ticket_id,
						'eventId'  => $event_id,
						'name'     => 'Original Name',
						'email'    => 'original@proof-of-concept.test',
					],
				],
			]
		);
		$attendee_id    = self::lodashGet( $create_response, 'data.registerAttendee.attendee.id' );
		$attendee_db_id = (int) self::lodashGet( $create_response, 'data.registerAttendee.attendee.databaseId' );
		$this->assertNotEmpty( $attendee_id, 'Seed attendee creation (as admin) should succeed.' );

		$original_title = get_post_field( 'post_title', $attendee_db_id );

		// Now attempt the update with no user context.
		$this->logout();

		$response = $this->graphql(
			[
				'query'     => $this->update_attendee_mutation(),
				'variables' => [
					'input' => [
						'attendeeId' => $attendee_id,
						'name'       => 'Hijacked Name',
						'email'      => 'stolen@attacker.test',
					],
				],
			]
		);

		$this->assertNotEmpty( $response['errors'] ?? null, 'Unauthenticated updateAttendee must return a GraphQL error.' );
		$this->assertStringContainsString( 'permission', strtolower( $response['errors'][0]['message'] ?? '' ) );
		$this->assertSame(
			$original_title,
			get_post_field( 'post_title', $attendee_db_id ),
			'Attendee title must not have been modified by an unauthenticated updateAttendee call.'
		);
	}
}
