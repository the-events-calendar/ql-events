<?php
class TicketPlusQueriesTest extends \QL_Events\Test\TestCase\QLEventsTestCase {
	public function testEventToTicketConnectionQuery() {
		// Generate events.
		$event_id      = $this->factory->event->create();

		// Generate ticket.
		$ticket_id = $this->factory->ticket->create_woocommerce_ticket( $event_id, 20 );

		// Query tickets.
		$query     = '
			query($id: ID!) {
				event(id: $id) {
					id
					databaseId
					tickets {
						nodes {
							... on SimpleProduct {
								id
								price
								__typename
							}
						}
					}
					wooTickets {
						nodes {
							id
							price
							__typename
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
					$this->expectedField( 'id', $this->toRelayId( 'product', $ticket_id ) ),
					$this->expectedField( 'price', '$20.00' ),
					$this->expectedField( '__typename', 'SimpleProduct' ),
				),
				0
			),
			$this->expectedNode(
				'event.wooTickets.nodes',
				array(
					$this->expectedField( 'id', $this->toRelayId( 'product', $ticket_id ) ),
					$this->expectedField( 'price', '$20.00' ),
					$this->expectedField( '__typename', 'SimpleProduct' ),
				),
				0
			),
		);
		$this->assertQuerySuccessful( $response, $expected );
	}

	public function testTicketToTicketFieldConnectionQueries() {
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
		$ticket_id = $this->factory->ticket->create_woocommerce_ticket( $event_id, 20 );

		// Create attendee meta.
		$checkbox_options = [
			'Test one',
			'Test two',
			'Test three',
		];

		$radio_options = [
			'Radio test one',
			'Radio test two',
			'Radio test three',
		];

		$select_options = [
			'Dropdown test one',
			'Dropdown test two',
			'Dropdown test three',
		];

		$data = [
			'tribe-tickets-input' => [
				[
					'type'        => 'checkbox',
					'label'       => 'Checkbox field test',
					'description' => 'test description',
					'extra'       => [
						'options' => implode( "\n", $checkbox_options ),
					],
				],
				[
					'type'        => 'radio',
					'label'       => 'Radio field test',
					'description' => 'test description',
					'extra'       => [
						'options' => implode( "\n", $radio_options ),
					],
				],
				[
					'type'        => 'text',
					'label'       => 'Text field test',
					'description' => 'test description',
					'required'    => 'on',
					'placeholder' => 'Enter text'
				],
				[
					'type'        => 'birth',
					'label'       => 'Birthdate field test',
					'description' => 'test description',
				],
				[
					'type'        => 'select',
					'label'       => 'Dropdown field test',
					'description' => 'test description',
					'extra'       => [
						'options' => implode( "\n", $select_options ),
					],
				],
				[
					'type'        => 'email',
					'label'       => 'Email field test',
					'description' => 'test description',
					'placeholder' => 'Enter email'
				],
				[
					'type'        => 'telephone',
					'label'       => 'Phone field test',
					'description' => 'test description',
					'placeholder' => 'Enter phone'
				],
				[
					'type'        => 'datetime',
					'label'       => 'Date field test',
					'description' => 'test description',
				],
				[
					'type'        => 'url',
					'label'       => 'URL field test',
					'description' => 'test description',
					'placeholder' => 'Enter URL'
				],
			],
		];

		$this->factory->ticket->add_attendee_meta_fields_to_ticket( $ticket_id, $data );

		$query = '
			query ($id: ID!) {
				event(id: $id) {
					ticketFields {
						type
						label
						description
						required
						... on TicketFieldCheckbox {
							options
						}
						... on TicketFieldDropdown {
							options
						}
						... on TicketFieldEmail {
							placeholder
						}
						... on TicketFieldPhone {
							placeholder
						}
						... on TicketFieldRadio {
							options
						}
						... on TicketFieldText {
							placeholder
						}
						... on TicketFieldURL {
							placeholder
						}
					}
					ticketFieldBirthdate {
						type
						label
						description
						required
					}
					ticketFieldCheckbox {
						type
						label
						description
						required
						options
					}
					ticketFieldDate {
						type
						label
						description
						required
					}
					ticketFieldDropdown {
						type
						label
						description
						required
						options
					}
					ticketFieldEmail {
						type
						label
						description
						required
						placeholder
					}
					ticketFieldPhone {
						type
						label
						description
						required
						placeholder
					}
					ticketFieldRadio {
						type
						label
						description
						required
						options
					}
					ticketFieldText {
						type
						label
						description
						required
						placeholder
					}
					ticketFieldURL {
						type
						label
						description
						required
						placeholder
					}
					wooTickets {
						nodes {
							name
							ticketFields {
								type
								label
								description
								required
								... on TicketFieldCheckbox {
									options
								}
								... on TicketFieldDropdown {
									options
								}
								... on TicketFieldEmail {
									placeholder
								}
								... on TicketFieldPhone {
									placeholder
								}
								... on TicketFieldRadio {
									options
								}
								... on TicketFieldText {
									placeholder
								}
								... on TicketFieldURL {
									placeholder
								}
							}
							ticketFieldBirthdate {
								type
								label
								description
								required
							}
							ticketFieldCheckbox {
								type
								label
								description
								required
								options
							}
							ticketFieldDate {
								type
								label
								description
								required
							}
							ticketFieldDropdown {
								type
								label
								description
								required
								options
							}
							ticketFieldEmail {
								type
								label
								description
								required
								placeholder
							}
							ticketFieldPhone {
								type
								label
								description
								required
								placeholder
							}
							ticketFieldRadio {
								type
								label
								description
								required
								options
							}
							ticketFieldText {
								type
								label
								description
								required
								placeholder
							}
							ticketFieldURL {
								type
								label
								description
								required
								placeholder
							}
						}
					}
				}
			}
		';

		$variables = [ 'id' => $this->toRelayId( 'post', $event_id ) ];
		$response  = $this->graphql( compact( 'query', 'variables' ) );

		// Create expected object.
		$expected_checkbox = [
			$this->expectedField( 'type', 'checkbox' ),
			$this->expectedField( 'label', 'Checkbox field test' ),
			$this->expectedField( 'description', 'test description' ),
			$this->expectedField( 'required', false ),
			$this->expectedField( 'options', $checkbox_options ),
		];
		$expected_radio    = [
			$this->expectedField( 'type', 'radio' ),
			$this->expectedField( 'label', 'Radio field test' ),
			$this->expectedField( 'description', 'test description' ),
			$this->expectedField( 'required', false ),
			$this->expectedField( 'options', $radio_options ),
		];
		$expected_text     = [
			$this->expectedField( 'type', 'text' ),
			$this->expectedField( 'label', 'Text field test' ),
			$this->expectedField( 'description', 'test description' ),
			$this->expectedField( 'required', true ),
			$this->expectedField( 'placeholder', 'Enter text' ),
		];
		$expected_birth    = [
			$this->expectedField( 'type', 'birth' ),
			$this->expectedField( 'label', 'Birthdate field test' ),
			$this->expectedField( 'description', 'test description' ),
		];
		$expected_select   = [
			$this->expectedField( 'type', 'select' ),
			$this->expectedField( 'label', 'Dropdown field test' ),
			$this->expectedField( 'description', 'test description' ),
		];
		$expected_email    = [
			$this->expectedField( 'type', 'email' ),
			$this->expectedField( 'label', 'Email field test' ),
			$this->expectedField( 'description', 'test description' ),
			$this->expectedField( 'placeholder', 'Enter email' ),
		];
		$expected_phone    = [
			$this->expectedField( 'type', 'telephone' ),
			$this->expectedField( 'label', 'Phone field test' ),
			$this->expectedField( 'description', 'test description' ),
			$this->expectedField( 'placeholder', 'Enter phone' ),
		];
		$expected_date     = [
			$this->expectedField( 'type', 'datetime' ),
			$this->expectedField( 'label', 'Date field test' ),
			$this->expectedField( 'description', 'test description' ),
		];
		$expected_url      = [
			$this->expectedField( 'type', 'url' ),
			$this->expectedField( 'label', 'URL field test' ),
			$this->expectedField( 'description', 'test description' ),
			$this->expectedField( 'placeholder', 'Enter URL' ),
		];


		$expected  = [
			$this->expectedNode( 'event.ticketFields', $expected_checkbox, 0 ),
			$this->expectedNode( 'event.ticketFieldCheckbox', $expected_checkbox, 0 ),
			$this->expectedNode( 'event.wooTickets.nodes.0.ticketFields', $expected_checkbox, 0 ),
			$this->expectedNode( 'event.wooTickets.nodes.0.ticketFieldCheckbox', $expected_checkbox, 0 ),

			$this->expectedNode( 'event.ticketFields', $expected_radio, 1 ),
			$this->expectedNode( 'event.ticketFieldRadio', $expected_radio, 0 ),
			$this->expectedNode( 'event.wooTickets.nodes.0.ticketFields', $expected_radio, 1 ),
			$this->expectedNode( 'event.wooTickets.nodes.0.ticketFieldRadio', $expected_radio, 0 ),

			$this->expectedNode( 'event.ticketFields', $expected_text, 2 ),
			$this->expectedNode( 'event.ticketFieldText', $expected_text, 0 ),
			$this->expectedNode( 'event.wooTickets.nodes.0.ticketFields', $expected_text, 2 ),
			$this->expectedNode( 'event.wooTickets.nodes.0.ticketFieldText', $expected_text, 0 ),

			$this->expectedNode( 'event.ticketFields', $expected_birth, 3 ),
			$this->expectedNode( 'event.ticketFieldBirthdate', $expected_birth, 0 ),
			$this->expectedNode( 'event.wooTickets.nodes.0.ticketFields', $expected_birth, 3 ),
			$this->expectedNode( 'event.wooTickets.nodes.0.ticketFieldBirthdate', $expected_birth, 0 ),

			$this->expectedNode( 'event.ticketFields', $expected_select, 4 ),
			$this->expectedNode( 'event.ticketFieldDropdown', $expected_select, 0 ),
			$this->expectedNode( 'event.wooTickets.nodes.0.ticketFields', $expected_select, 4 ),
			$this->expectedNode( 'event.wooTickets.nodes.0.ticketFieldDropdown', $expected_select, 0 ),

			$this->expectedNode( 'event.ticketFields', $expected_email, 5 ),
			$this->expectedNode( 'event.ticketFieldEmail', $expected_email, 0 ),
			$this->expectedNode( 'event.wooTickets.nodes.0.ticketFields', $expected_email, 5 ),
			$this->expectedNode( 'event.wooTickets.nodes.0.ticketFieldEmail', $expected_email, 0 ),

			$this->expectedNode( 'event.ticketFields', $expected_phone, 6 ),
			$this->expectedNode( 'event.ticketFieldPhone', $expected_phone, 0 ),
			$this->expectedNode( 'event.wooTickets.nodes.0.ticketFields', $expected_phone, 6 ),
			$this->expectedNode( 'event.wooTickets.nodes.0.ticketFieldPhone', $expected_phone, 0 ),

			$this->expectedNode( 'event.ticketFields', $expected_date, 7 ),
			$this->expectedNode( 'event.ticketFieldDate', $expected_date, 0 ),
			$this->expectedNode( 'event.wooTickets.nodes.0.ticketFields', $expected_date, 7 ),
			$this->expectedNode( 'event.wooTickets.nodes.0.ticketFieldDate', $expected_date, 0 ),

			$this->expectedNode( 'event.ticketFields', $expected_url, 8 ),
			$this->expectedNode( 'event.ticketFieldURL', $expected_url, 0 ),
			$this->expectedNode( 'event.wooTickets.nodes.0.ticketFields', $expected_url, 8 ),
			$this->expectedNode( 'event.wooTickets.nodes.0.ticketFieldURL', $expected_url, 0 ),
		];

		// Assert response passes our expected rules.
		$this->assertQuerySuccessful( $response, $expected );
	}
}
