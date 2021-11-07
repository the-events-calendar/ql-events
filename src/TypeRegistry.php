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
		// Types.
		WPObject\VenueCoordinates::register_type();
		WPObject\EventLinkedData::register_type();
		WPObject\OrganizerLinkedData::register_type();
		WPObject\VenueLinkedData::register_type();

		// Object Fields.
		WPObject\Venue::register_fields();
		WPObject\Event::register_fields();
		WPObject\Organizer::register_fields();

		// Connections.
		Connection\Events::register_connections();
		Connection\Organizers::register_connections();
		Connection\Venues::register_connections();

		// tickets.

		// other.
	}
}
