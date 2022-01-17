<?php
/**
 * GraphQL Input Type - DateRangeAndTimezoneInput
 *
 * @package WPGraphQL\TEC\Events\Type\Input
 * @since 0.0.1
 */

namespace WPGraphQL\TEC\Events\Type\Input;

/**
 * Class - DateAndTimezoneInput
 */
class DateRangeAndTimezoneInput {

	/**
	 * Name of the type.
	 *
	 * @var string Type name.
	 */
	public static $type = 'DateRangeAndTimezoneInput';
	/**
	 * {@inheritDoc}
	 */
	public static function register_type() : void {
		register_graphql_input_type(
			self::$type,
			[
				'description' => __( 'Date range and timezone values', 'wp-graphql-tec' ),
				'fields'      => [
					'startDateTime' => [
						'type'        => 'String',
						'description' => __( 'A `strtotime` parsable string or a timestamp', 'wp-graphql-tec' ),
					],
					'endDateTime'   => [
						'type'        => 'String',
						'description' => __( 'A `strtotime` parsable string or a timestamp', 'wp-graphql-tec' ),
					],
					'timezone'      => [
						'type'        => 'String',
						'description' => __( 'A timezone string or UTC offset. Defaults to the site\'s timezone', 'wp-graphql-tec' ),
					],
				],
			]
		);
	}
}
