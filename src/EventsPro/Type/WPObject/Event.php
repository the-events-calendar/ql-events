<?php
/**
 * Extends the GraphQL Object Type - Event
 *
 * @package WPGraphQL\TEC\EventsPro\Type\WPObject
 * @since 0.0.1
 */

namespace WPGraphQL\TEC\EventsPro\Type\WPObject;

/**
 * Class - Event
 */
class Event {
	/**
	 * Name of the type.
	 *
	 * @var string Type name.
	 */
	public static $type = 'Event';

	/**
	 * {@inheritDoc}
	 */
	public static function register_fields() : void {
		register_graphql_fields(
			self::$type,
			[
				'isRecurring'       => [
					'type'        => 'Boolean',
					'description' => __( 'Whether the event is one of a set of recurring events.', 'wp-graphql-tec' ),
				],
				'recurrenceDetails' => [
					'type'        => RecurrenceDetails::$type,
					'description' => __( 'The link for all occurrences of an event', 'wp-graphql-tec' ),
					'resolve'     => fn( $source ) => $source,
				],
			]
		);
	}
}
