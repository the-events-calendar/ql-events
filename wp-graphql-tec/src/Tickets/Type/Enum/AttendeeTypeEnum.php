<?php
/**
 * Type AttendeeTypeEnum
 *
 * @package WPGraphQL\TEC\Tickets\Type\Enum
 * @since   0.0.1
 */

namespace WPGraphQL\TEC\Tickets\Type\Enum;

use WPGraphQL\TEC\TEC;
use WPGraphQL\TEC\Utils\Utils;

/**
 * Class - AttendeeTypeEnum
 */
class AttendeeTypeEnum {
	/**
	 * Name of the type.
	 *
	 * @var string Type name.
	 */
	public static $type = 'AttendeeTypeEnum';

	/**
	 * Registers the GraphQL type
	 */
	public static function register_type() : void {
		register_graphql_enum_type(
			self::$type,
			[
				'description' => __( 'The attendee post type.', 'wp-graphql-tec' ),
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
		$attendee_types = Utils::get_et_attendee_types();
		$values         = [];

		foreach ( $attendee_types as $value => $name ) {
			$values[ $value ] = [
				'name'        => strtoupper( str_replace( 'Attendee', '_Attendee', $name ) ),
				'value'       => $value,
				/* translators: GraphQL ticket type name */
				'description' => sprintf( __( 'A %s attendee type', 'wp-graphql-tec' ), $name ),
			];
		}
		return $values;
	}
}
