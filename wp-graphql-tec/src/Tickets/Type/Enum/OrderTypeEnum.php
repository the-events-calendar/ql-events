<?php
/**
 * Type OrderTypeEnum
 *
 * @package WPGraphQL\TEC\Tickets\Type\Enum
 * @since   0.0.1
 */

namespace WPGraphQL\TEC\Tickets\Type\Enum;

use WPGraphQL\TEC\TEC;
use WPGraphQL\TEC\Utils\Utils;

/**
 * Class - OrderTypeEnum
 */
class OrderTypeEnum {
	/**
	 * Name of the type.
	 *
	 * @var string Type name.
	 */
	public static $type = 'OrderTypeEnum';

	/**
	 * Registers the GraphQL type
	 */
	public static function register_type() : void {
		register_graphql_enum_type(
			self::$type,
			[
				'description' => __( 'The Order post type.', 'wp-graphql-tec' ),
				'values'      => self::get_values(),
			]
		);
	}

	/**
	 * Generates the Enum values for the config.
	 *
	 * @return array
	 */
	public static function get_values() : array {
		$order_types = Utils::get_et_order_types();
		$values      = [];

		foreach ( $order_types as $value => $name ) {
			$values[ $value ] = [
				'name'        => strtoupper( str_replace( 'Order', '_Order', $name ) ),
				'value'       => $value,
				/* translators: GraphQL ticket type name */
				'description' => sprintf( __( 'A %s Order type', 'wp-graphql-tec' ), $name ),
			];
		}
		return $values;
	}
}
