<?php
/**
 * Connection - Tickets.
 *
 * Registers connections from other types to Tickets.
 *
 * @package \WPGraphQL\TEC\Tickets\Connection
 * @since   0.0.1
 */

namespace WPGraphQL\TEC\Tickets\Connection;

use WPGraphQL\TEC\Tickets\Data\TicketHelper;
use WPGraphQL\TEC\Tickets\Type\WPInterface\Ticket;

/**
 * Class - Tickets
 */
class Tickets {
	/**
	 * The GraphQL field name for the connection.
	 *
	 * @var string
	 */
	public static string $from_field_name = 'tickets';

	/**
	 * Registers the various connections from other Types to Tickets.
	 */
	public static function register_connections() : void {
		register_graphql_connection(
			TicketHelper::get_connection_config(
				[
					'fromType'      => 'RootQuery',
					'toType'        => Ticket::$type,
					'fromFieldName' => self::$from_field_name,
				]
			)
		);
	}
}
