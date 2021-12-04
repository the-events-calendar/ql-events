<?php
/**
 * GraphQL Object Type - RecurrenceDetails
 *
 * @package WPGraphQL\TEC\EventsPro\Type\WPObject
 * @since 0.0.1
 */

namespace WPGraphQL\TEC\EventsPro\Type\WPObject;

use WPGraphQL\TEC\Utils\Utils;
use WPGraphQL\Type\WPEnumType;

/**
 * Class - RecurrenceDetails
 */
class RecurrenceDetails {

	/**
	 * Name of the type.
	 *
	 * @var string Type name.
	 */
	public static $type = 'RecurrenceDetails';

	/**
	 * {@inheritDoc}
	 */
	public static function register_type() : void {
		register_graphql_object_type(
			self::$type,
			[
				'description' => __( 'Event recurrence details', 'wp-graphql-tec' ),
				'fields'      => [
					'permalinkAll'   => [
						'type'        => 'String',
						'description' => __( 'The link for all occurrences of an event', 'wp-graphql-tec' ),
					],
					'recurrenceText' => [
						'type'        => 'String',
						'description' => __( 'The textual version of event recurrence. E.g. `Repeats daily for three days`', 'wp-graphql-tec' ),
					],
					'startDates'     => [
						'type'        => [ 'list_of' => 'String' ],
						'description' => __( 'A list of start dates of all occurrences of an event, in ascending order (Y-m-d H:i:s)', 'wp-graphql-tec' ),
					],
					'icalLink'       => [
						'type'        => 'String',
						'description' => __( 'The link to export the whole recurring series in iCal format', 'wp-graphql-tec' ),
						'resolve'     => fn( $source) => $source->recurrenceIcalLink,

					],
				],
			]
		);
	}
}
