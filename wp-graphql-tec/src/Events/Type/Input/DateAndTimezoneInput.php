<?php
/**
 * GraphQL Input Type - DateAndTimezoneInput
 *
 * @package WPGraphQL\TEC\Events\Type\Input
 * @since 0.0.1
 */

namespace WPGraphQL\TEC\Events\Type\Input;

/**
 * Class - DateAndTimezoneInput
 */
class DateAndTimezoneInput {

	/**
	 * Name of the type.
	 *
	 * @var string Type name.
	 */
	public static $type = 'DateAndTimezoneInput';
	/**
	 * {@inheritDoc}
	 */
	public static function register_type() : void {
		register_graphql_input_type(
			self::$type,
			[
				'description' => __( 'Date and timezone values', 'wp-graphql-tec' ),
				'fields'      => [
					'dateTime' => [
						'type'        => 'String',
						'description' => __( 'A `strtotime` parsable string or a timestamp', 'wp-graphql-tec' ),
					],
					'timezone' => [
						'type'        => 'String',
						'description' => __( 'A timezone string or UTC offset. Defaults to the site\'s timezone', 'wp-graphql-tec' ),
					],
				],
			]
		);
	}
}
