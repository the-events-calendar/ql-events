<?php
/**
 * GraphQL Object Type - RecurrenceDetails
 *
 * @package WPGraphQL\TEC\EventsPro\Type\WPObject
 * @since 0.0.1
 */

namespace WPGraphQL\TEC\EventsPro\Type\WPObject;

use WPGraphQL\TEC\Events\Data\EventHelper;
use WPGraphQL\TEC\Events\Type\WPObject\Event;
use WPGraphQL\AppContext;
use GraphQL\Type\Definition\ResolveInfo;
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
				'connections' => [
					'eventsInSeries' => [
						'toType'  => Event::$type,
						'resolve' => function ( $source, array $args, AppContext $context, ResolveInfo $info ) {
							if ( null === $source->parentDatabaseId ) {
								return null;
							}

							$args['where']['inSeries'] = $source->parentDatabaseId;

							return EventHelper::resolve_connection( $source, $args, $context, $info, 'tribe_events' );
						},
					],
				],
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
