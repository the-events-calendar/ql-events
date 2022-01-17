<?php
/**
 * Connection - Attendees.
 *
 * Registers connections from other types to Attendees.
 *
 * @package \WPGraphQL\TEC\Tickets\Connection
 * @since   0.0.1
 */

namespace WPGraphQL\TEC\Tickets\Connection;

use WPGraphQL\TEC\Tickets\Data\AttendeeHelper;
use WPGraphQL\TEC\Tickets\Type\WPInterface\Attendee;

/**
 * Class - Attendees
 */
class Attendees {
	/**
	 * The GraphQL field name for the connection.
	 *
	 * @var string
	 */
	public static string $from_field_name = 'attendees';

	/**
	 * Registers the various connections from other Types to Attendees.
	 */
	public static function register_connections() : void {
		register_graphql_connection(
			AttendeeHelper::get_connection_config(
				[
					'fromType'      => 'RootQuery',
					'toType'        => Attendee::$type,
					'fromFieldName' => self::$from_field_name,
				]
			)
		);
	}
}
