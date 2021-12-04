<?php
/**
 * Registers DistanceUnitEnum
 *
 * @package WPGraphQL\TEC\EventsPro\Type\Enum
 * @since   0.0.1
 */

namespace WPGraphQL\TEC\EventsPro\Type\Enum;

/**
 * Class - DistanceUnitEnum
 */
class DistanceUnitEnum {
	/**
	 * Name of the type.
	 *
	 * @var string Type name.
	 */
	public static $type = 'DistanceUnitEnum';

	/**
	 * Registers the GraphQL type
	 */
	public static function register_type() : void {
		register_graphql_enum_type(
			self::$type,
			[
				'description' => __( 'Distance units', 'wp-graphql-tec' ),
				'values'      => [
					'miles' => [
						'name'       => 'MILES',
						'value'      => 'miles',
						'desciption' => __( 'Miles.', 'wp-graphql-tec' ),
					],
					'kms'   => [
						'name'       => 'KILOMETERS',
						'value'      => 'kms',
						'desciption' => __( 'Kilometers.', 'wp-graphql-tec' ),
					],
				],
			]
		);
	}
}
