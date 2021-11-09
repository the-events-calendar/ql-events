<?php
/**
 * Register *StockModeEnum
 *
 * @see Tribe__Tickets__Global_Stock
 *
 * @package WPGraphQL\TEC\Type\Enum
 * @since   0.0.1
 */

namespace WPGraphQL\TEC\Type\Enum;

/**
 * Class - StockModeEnum
 */
class StockModeEnum {
	/**
	 * Name of the type.
	 *
	 * @var string Type name.
	 */
	public static $type = 'StockModeEnum';

	/**
	 * Registers the GraphQL type
	 */
	public static function register_type() : void {
		register_graphql_enum_type(
			self::$type,
			[
				'description' => __( 'The mode used to indicate how the stock is handled.', 'wp-graphql-tec' ),
				'values'      => [
					'global' => [
						'name'       => 'GLOBAL',
						'value'      => 'global',
						'desciption' => __( 'The ticket uses the global stock.', 'wp-graphql-tec' ),
					],
					'capped' => [
						'name'        => 'CAPPED',
						'value'       => 'capped',
						'description' => __( 'The ticket will use the global stock, but that a cap has been placed on the total number of sales for this ticket type.', 'wp-graphql-tec' ),
					],
					'own'    => [
						'name'        => 'OWN',
						'value'       => 'own',
						'description' => __( 'If global stock is in effect for an event, the specific ticket this flag is applied to will maintain it\'s own inventory rather than draw from the global', 'wp-graphql-tec' ),
					],
				],
			]
		);
	}
}
