<?php
/**
 * Registers TEC types to the schema.
 *
 * @package \WPGraphQL\QL_Events
 * @since   0.0.1
 */

namespace WPGraphQL\QL_Events;

/**
 * Class Type_Registry
 */
class Type_Registry {
	/**
	 * Registers QL Events types, connections, unions, and mutations to GraphQL schema
	 *
	 * @param \WPGraphQL\Registry\TypeRegistry $type_registry  Instance of the WPGraphQL TypeRegistry.
	 */
	public function init( \WPGraphQL\Registry\TypeRegistry $type_registry ) {
		// TEC Object fields.
		\WPGraphQL\QL_Events\Type\WPObject\Event_Type::register_fields();
		\WPGraphQL\QL_Events\Type\WPObject\Event_Linked_Data_Type::register();
		\WPGraphQL\QL_Events\Type\WPObject\Organizer_Type::register_fields();
		\WPGraphQL\QL_Events\Type\WPObject\Organizer_Linked_Data_Type::register();
		\WPGraphQL\QL_Events\Type\WPObject\Venue_Type::register_fields();
		\WPGraphQL\QL_Events\Type\WPObject\Venue_Linked_Data_Type::register();

		// TEC Connections.
		\WPGraphQL\QL_Events\Connection\Organizers::register_connections();

		// Register type fields if Event Tickets in installed and loaded.
		if ( \QL_Events::is_ticket_events_loaded() ) {
			\WPGraphQL\QL_Events\Type\WPObject\PayPalAttendee_Type::register_fields();
			\WPGraphQL\QL_Events\Type\WPObject\PayPalOrder_Type::register_fields();
			\WPGraphQL\QL_Events\Type\WPObject\PayPalTicket_Type::register_fields();
			\WPGraphQL\QL_Events\Type\WPObject\RSVPAttendee_Type::register_fields();
			\WPGraphQL\QL_Events\Type\WPObject\RSVPTicket_Type::register_fields();

			// Event Tickets Connections.
			\WPGraphQL\QL_Events\Connection\Tickets::register_connections();
			\WPGraphQL\QL_Events\Connection\RSVPAttendees::register_connections();
			\WPGraphQL\QL_Events\Connection\PayPalAttendees::register_connections();
		}

		if ( \QL_Events::is_ticket_events_plus_loaded() ) {
			\WPGraphQL\QL_Events\Type\WPObject\WooAttendee_Type::register_fields();
			\WPGraphQL\QL_Events\Type\WPObject\Ticket_Linked_Data_Type::register();

			// Event Tickets Plus Connections.
			\WPGraphQL\QL_Events\Connection\Tickets_Plus::register_connections();
			\WPGraphQL\QL_Events\Connection\WooAttendees::register_connections();

		}
	}
}
