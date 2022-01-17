<?php
/**
 * Type StockHandlingOptionsEnum
 *
 * @package WPGraphQL\TEC\Tickets\Type\Enum
 * @since   0.0.1
 */

namespace WPGraphQL\TEC\Tickets\Type\Enum;

/**
 * Class - StockHandlingOptionsEnum
 */
class StockHandlingOptionsEnum {
	/**
	 * Name of the type.
	 *
	 * @var string Type name.
	 */
	public static $type = 'StockHandlingOptionsEnum';

	/**
	 * Registers the GraphQL type
	 */
	public static function register_type() : void {
		register_graphql_enum_type(
			self::$type,
			[
				'description' => __( 'Location of tickets form.', 'wp-graphql-tec' ),
				'values'      => [
					'on-pending'  => [
						'name'        => 'ON_PENDING',
						'value'       => 'on-pending',
						'description' => __( 'Decrease available ticket stock as soon as a Pending order is created.', 'wp-graphql-tec' ),
					],
					'on-complete' => [
						'name'        => 'ON_COMPLETE',
						'value'       => 'on-complete',
						'description' => __( 'Only decrease available ticket stock if an order is confirmed as Completed by PayPal.', 'wp-graphql-tec' ),
					],
				],
			]
		);
	}
}
