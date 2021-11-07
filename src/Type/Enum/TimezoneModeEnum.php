<?php
/**
 * Register *TimezoneModeEnum
 *
 * @package WPGraphQL\TEC\Type\Enum
 * @since   0.0.1
 */

namespace WPGraphQL\TEC\Type\Enum;

/**
 * Class - TimezoneModeEnum
 */
class TimezoneModeEnum {
	/**
	 * Name of the type.
	 *
	 * @var string Type name.
	 */
	public static $type = 'TimezoneModeEnum';

	/**
	 * Registers the GraphQL type
	 */
	public static function register_type() : void {
		register_graphql_enum_type(
			self::$type,
			[
				'description' => __( 'The position of the currency symbol.', 'wp-graphql-tec' ),
				'values'      => [
					'event' => [
						'name'       => 'EVENT',
						'value'      => 'event',
						'desciption' => __( 'Use manual time zones for each event.', 'wp-graphql-tec' ),
					],
					'site'  => [
						'name'        => 'SITE',
						'value'       => 'site',
						'description' => __( 'Use the site-wide time zone everywhere.', 'wp-graphql-tec' ),
					],
				],
			]
		);
	}
}
