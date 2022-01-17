<?php
/**
 * Connection - Orders.
 *
 * Registers connections from other types to Orders.
 *
 * @package \WPGraphQL\TEC\Tickets\Connection
 * @since   0.0.1
 */

namespace WPGraphQL\TEC\Tickets\Connection;

use WPGraphQL\TEC\Tickets\Data\OrderHelper;
use WPGraphQL\TEC\Tickets\Type\WPInterface\Order;

/**
 * Class - Orders
 */
class Orders {
	/**
	 * The GraphQL field name for the connection.
	 *
	 * @var string
	 */
	public static string $from_field_name = 'ticketOrders';

	/**
	 * Registers the various connections from other Types to Orders.
	 */
	public static function register_connections() : void {
		register_graphql_connection(
			OrderHelper::get_connection_config(
				[
					'fromType'      => 'RootQuery',
					'toType'        => Order::$type,
					'fromFieldName' => self::$from_field_name,
				]
			)
		);
	}
}
