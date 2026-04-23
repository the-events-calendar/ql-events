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
	 * Exact error message thrown by the registerAttendee mutation when permission
	 * is denied. Pinned as a constant so tests flag any accidental rewording — a
	 * feature for a security-critical error surface.
	 */
	private const ERR_REGISTER_FORBIDDEN = 'You do not have permission to register attendees for this event.';

	/**
	 * Exact error message thrown by the updateAttendee mutation when permission
	 * is denied.
	 */
	private const ERR_UPDATE_FORBIDDEN = 'You do not have permission to update this attendee.';

	/**
	 * Seeds a published event with a PayPal (TPP) ticket and returns their IDs.
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

	/**
	 * Seeds an event, ticket, and attendee using admin privileges. Leaves the
	 * current user logged in as admin — callers switch identity explicitly when
	 * they want to exercise the negative path.
	 *
	 * @return array{event_id:int,ticket_id:int,attendee_id:string,attendee_db_id:int}
	 */
	private function seed_attendee_as_admin(): array {
		$this->loginAs( 1 );

		[ 'event_id' => $event_id, 'ticket_id' => $ticket_id ] = $this->seed_event_and_ticket();

		$response = $this->graphql(
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

		$attendee_id    = self::lodashGet( $response, 'data.registerAttendee.attendee.id' );
		$attendee_db_id = (int) self::lodashGet( $response, 'data.registerAttendee.attendee.databaseId' );
		$this->assertNotEmpty( $attendee_id, 'Seed attendee creation (as admin) should succeed.' );

		return [
			'event_id'       => $event_id,
			'ticket_id'      => $ticket_id,
			'attendee_id'    => $attendee_id,
			'attendee_db_id' => $attendee_db_id,
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
	 * Asserts the GraphQL response contains exactly the expected top-level error
	 * message. Using exact equality (rather than a lowercase substring match)
	 * means any change to the error text forces a reviewer to touch the test.
	 */
	private function assert_graphql_error( array $response, string $expected_message ): void {
		$this->assertNotEmpty( $response['errors'] ?? null, 'Expected a GraphQL error response.' );
		$this->assertSame( $expected_message, $response['errors'][0]['message'] ?? null );
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

		$this->assert_graphql_error( $response, self::ERR_REGISTER_FORBIDDEN );
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

		$this->assert_graphql_error( $response, self::ERR_REGISTER_FORBIDDEN );
	}

	/**
	 * The `ql_events_user_can_register_attendee` filter must be able to grant
	 * access to a user who would otherwise fail the default capability check.
	 * Guards the documented extension point from silent regressions.
	 */
	public function testRegisterAttendeeFilterCanGrantAccess() {
		$subscriber_id = $this->factory()->user->create( [ 'role' => 'subscriber' ] );
		$this->loginAs( $subscriber_id );

		[ 'event_id' => $event_id, 'ticket_id' => $ticket_id ] = $this->seed_event_and_ticket();

		add_filter( 'ql_events_user_can_register_attendee', '__return_true' );

		try {
			$response = $this->graphql(
				[
					'query'     => $this->register_attendee_mutation(),
					'variables' => [
						'input' => [
							'ticketId' => $ticket_id,
							'eventId'  => $event_id,
							'name'     => 'Filter Granted',
							'email'    => 'filter@proof-of-concept.test',
						],
					],
				]
			);

			$this->assertQuerySuccessful(
				$response,
				[
					$this->expectedField( 'registerAttendee.attendee', self::NOT_NULL ),
					$this->expectedField( 'registerAttendee.attendee.fullName', 'Filter Granted' ),
				]
			);
		} finally {
			remove_filter( 'ql_events_user_can_register_attendee', '__return_true' );
		}
	}

	/**
	 * An anonymous caller must not be able to modify an existing attendee.
	 */
	public function testUpdateAttendeeRejectsUnauthenticatedRequests() {
		$seed = $this->seed_attendee_as_admin();

		$this->logout();

		$response = $this->graphql(
			[
				'query'     => $this->update_attendee_mutation(),
				'variables' => [
					'input' => [
						'attendeeId' => $seed['attendee_id'],
						'name'       => 'Hijacked Name',
						'email'      => 'stolen@attacker.test',
					],
				],
			]
		);

		$this->assert_graphql_error( $response, self::ERR_UPDATE_FORBIDDEN );
		$this->assertSame(
			'Original Name',
			get_post_field( 'post_title', $seed['attendee_db_id'] ),
			'Attendee title must not have been modified by an unauthenticated updateAttendee call.'
		);
	}

	/**
	 * An authenticated user without `edit_post` on the parent event must not be
	 * able to modify an existing attendee.
	 */
	public function testUpdateAttendeeRejectsUsersWithoutEditCapability() {
		$seed = $this->seed_attendee_as_admin();

		$subscriber_id = $this->factory()->user->create( [ 'role' => 'subscriber' ] );
		$this->loginAs( $subscriber_id );

		$response = $this->graphql(
			[
				'query'     => $this->update_attendee_mutation(),
				'variables' => [
					'input' => [
						'attendeeId' => $seed['attendee_id'],
						'name'       => 'Subscriber Overreach',
						'email'      => 'sub@attacker.test',
					],
				],
			]
		);

		$this->assert_graphql_error( $response, self::ERR_UPDATE_FORBIDDEN );
		$this->assertSame(
			'Original Name',
			get_post_field( 'post_title', $seed['attendee_db_id'] ),
			'Attendee title must not have been modified by a subscriber-level updateAttendee call.'
		);
	}

	/**
	 * The `ql_events_user_can_update_attendee` filter must be able to grant access
	 * to a user who would otherwise fail the default capability check.
	 */
	public function testUpdateAttendeeFilterCanGrantAccess() {
		$seed = $this->seed_attendee_as_admin();

		$subscriber_id = $this->factory()->user->create( [ 'role' => 'subscriber' ] );
		$this->loginAs( $subscriber_id );

		add_filter( 'ql_events_user_can_update_attendee', '__return_true' );

		try {
			$response = $this->graphql(
				[
					'query'     => $this->update_attendee_mutation(),
					'variables' => [
						'input' => [
							'attendeeId' => $seed['attendee_id'],
							'name'       => 'Filter Granted Update',
							'email'      => 'filter-update@proof-of-concept.test',
						],
					],
				]
			);

			$this->assertQuerySuccessful(
				$response,
				[
					$this->expectedField( 'updateAttendee.attendee.databaseId', $seed['attendee_db_id'] ),
					$this->expectedField( 'updateAttendee.attendee.fullName', 'Filter Granted Update' ),
				]
			);
		} finally {
			remove_filter( 'ql_events_user_can_update_attendee', '__return_true' );
		}
	}
}
