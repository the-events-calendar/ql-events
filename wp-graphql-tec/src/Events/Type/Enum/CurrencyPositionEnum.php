<?php
/**
 * Register *CurrencyPositionEnum
 *
 * @package WPGraphQL\TEC\Events\Type\Enum
 * @since   0.0.1
 */

namespace WPGraphQL\TEC\Events\Type\Enum;

/**
 * Class - CurrencyPositionEnum
 */
class CurrencyPositionEnum {
	/**
	 * Name of the type.
	 *
	 * @var string Type name.
	 */
	public static $type = 'CurrencyPositionEnum';

	/**
	 * Registers the GraphQL type
	 */
	public static function register_type() : void {
		register_graphql_enum_type(
			self::$type,
			[
				'description' => __( 'The position of the currency symbol.', 'wp-graphql-tec' ),
				'values'      => [
					'prefix' => [
						'name'       => 'PREFIX',
						'value'      => 'prefix',
						'desciption' => __( 'Display before the cost.', 'wp-graphql-tec' ),
					],
					'suffix' => [
						'name'        => 'SUFFIX',
						'value'       => 'suffix',
						'description' => __( 'Display after the cost.', 'wp-graphql-tec' ),
					],
				],
			]
		);
	}

}
