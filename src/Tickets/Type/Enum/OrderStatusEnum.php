<?php
/**
 * Type OrderStatusEnum
 *
 * @package WPGraphQL\TEC\Tickets\Type\Enum
 * @since   0.0.1
 */

namespace WPGraphQL\TEC\Tickets\Type\Enum;

use WPGraphQL\TEC\Utils\Utils;

/**
 * Class - OrderStatusEnum
 */
class OrderStatusEnum {
	/**
	 * Name of the type.
	 *
	 * @var string Type name.
	 */
	public static $type = 'OrderStatusEnum';

	/**
	 * Registers the GraphQL type
	 */
	public static function register_type() : void {
		$status_type = 'Rsvp';
		register_graphql_enum_type(
			$status_type . self::$type,
			[
				'description' => __( 'The order status.', 'wp-graphql-tec' ),
				'values'      => self::get_values( $status_type ),
			]
		);
	}

	/**
	 * Generates the Enum values for the config.
	 *
	 * @param string $type The ticket type.
	 */
	public static function get_values( string $type ) : array {
		$statuses = false;
		switch ( $type ) {
			case 'Rsvp':
				$statuses = tribe( 'TEC\Tickets\Commerce\Tickets_View' )->get_rsvp_options();
		}

		if ( false === $statuses ) {
			return [];
		}

		$values = [];
		foreach ( $statuses as $value => $name ) {
			$values[ $value ] = [
				'name'        => strtoupper( str_replace( ' ', '_', $name ) ),
				'value'       => $value,
				/* translators: GraphQL ticket type name */
				'description' => sprintf( __( 'A %1$s %2$s status', 'wp-graphql-tec' ), $name, $type ),
			];
		}

		return $values;
	}
}
