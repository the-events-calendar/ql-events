<?php
/**
 * Registers TEC types to the schema.
 *
 * @package \WPGraphQL\TEC
 * @since   0.0.1
 */

namespace WPGraphQL\TEC;

use WPGraphQL\Registry\TypeRegistry as GraphQLRegistry;
use WPGraphQL\TEC\Type\WPObject;
use WPGraphQL\TEC\Type\Enum;
use WPGraphQL\TEC\Type\WPInterface;
use WPGraphQL\TEC\Connection;
/**
 * Class Type_Registry
 */
class TypeRegistry {
	/**
	 * Registers QL Events types, connections, unions, and mutations to GraphQL schema
	 *
	 * @param GraphQLRegistry $type_registry  Instance of the WPGraphQL TypeRegistry.
	 */
	public function init( GraphQLRegistry $type_registry ) : void {
		// Enums.
		Enum\CurrencyPositionEnum::register_type();
		Enum\EnabledViewsEnum::register_type();
		Enum\EventsTemplateEnum::register_type();
		Enum\PaypalCurrencyCodeOptionsEnum::register_type();
		Enum\StockHandlingOptionsEnum::register_type();
		Enum\StockModeEnum::register_type();
		Enum\TicketIdTypeEnum::register_type();
		Enum\TicketTypeEnum::register_type();
		Enum\TimezoneModeEnum::register_type();
		Enum\TicketFormLocationOptionsEnum::register_type();
		// Interfaces.
		WPInterface\Ticket::register_interface( $type_registry );
		WPInterface\PurchasableTicket::register_interface( $type_registry );
		// Types.
		WPObject\EventLinkedData::register_type();
		WPObject\OrganizerLinkedData::register_type();
		WPObject\TecSettings::register_type();
		WPObject\VenueCoordinates::register_type();
		WPObject\VenueLinkedData::register_type();

		// Object Fields.
		WPObject\Venue::register_fields();
		WPObject\Event::register_fields();
		WPObject\Organizer::register_fields();
		WPObject\RsvpTicket::register_fields();

		// Connections.
		Connection\Events::register_connections();
		Connection\Organizers::register_connections();
		Connection\Venues::register_connections();

		// tickets.

		// other.
	}
}
