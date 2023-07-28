<?php
/**
 * Connection - Events
 *
 * Registers connections to Events
 *
 * @package WPGraphQL\QL_Events\Connection
 * @since   0.0.1
 */

namespace WPGraphQL\QL_Events\Connection;

use Tribe__Events__Main as Main;
use GraphQL\Error\UserError;
use WPGraphQL\QL_Events\Utils\Events_Query;
use WPGraphQL\Type\Connection\PostObjects;
use WPGraphQL\Data\Connection\PostObjectConnectionResolver;

/**
 * Class - Events
 */
class Events extends PostObjects {

	/**
	 * Register event connections.
	 *
	 * @since TBD
	 *
	 * @return void
	 */
	public static function register_connections() {
		// From RootQuery to Events.
		register_graphql_connection(
			[
				'fromType'       => 'RootQuery',
				'toType'         => 'Event',
				'fromFieldName'  => 'events',
				'connectionArgs' => self::get_connection_args(),
				'resolve'        => function( $source, $args, $context, $info ) {
					$context->queryClass = Events_Query::class;
					$resolver = new PostObjectConnectionResolver( $source, $args, $context, $info, Main::POSTTYPE );

					$resolver->set_query_arg( 'order', 'ASC' );
					$resolver->set_query_arg( 'orderby', 'event_date' );
					return $resolver->get_connection();
				},
			]
		);
	}


	/**
	 * Returns Event connection where arguments.
	 *
	 * @since 0.0.1
	 *
	 * @param array  $_  Unused.
	 * @param string $_2 Unused.
	 *
	 * @return array
	 */
	public static function get_connection_args( $_ = [], $_2 = Main::POSTTYPE ) {
		$connection_args = parent::get_connection_args(
			[
				'startsAfter'          => [
					'type'        => 'String',
					'description' => __( 'Include events that start after.', 'ql-events' ),
				],
				'startsBefore'         => [
					'type'        => 'String',
					'description' => __( 'Include events that start before.', 'ql-events' ),
				],
				'startsOnOrAfter'      => [
					'type'        => 'String',
					'description' => __( 'Include events that start on or after.', 'ql-events' ),
				],
				'startDate'            => [
					'type'        => 'String',
					'description' => __( 'Include events that start at.', 'ql-events' ),
				],
				'endsAfter'            => [
					'type'        => 'String',
					'description' => __( 'Include events that end after.', 'ql-events' ),
				],
				'endsBefore'           => [
					'type'        => 'String',
					'description' => __( 'Include events that end before.', 'ql-events' ),
				],
				'endsOnOrAfter'        => [
					'type'        => 'String',
					'description' => __( 'Include events that end on or after.', 'ql-events' ),
				],
				'endDate'              => [
					'type'        => 'String',
					'description' => __( 'Include events that end at.', 'ql-events' ),
				],
				'dateOverlaps'         => [
					'type'        => 'TECDateRangeInput',
					'description' => __( 'Include events that overlap with.', 'ql-events' ),
				],
				'runsBetween'          => [
					'type'        => 'TECDateRangeInput',
					'description' => __( 'Include events that run between.', 'ql-events' ),
				],
				'onDate'               => [
					'type'        => 'String',
					'description' => __( 'Include events that end at.', 'ql-events' ),
				],

				'allDay'               => [
					'type'        => 'Boolean',
					'description' => __( 'Include all-day events.', 'ql-events' ),
				],
				'multiday'             => [
					'type'        => 'Boolean',
					'description' => __( 'Include events spanning multiple days.', 'ql-events' ),
				],
				'onCalendarGrid'       => [
					'type'        => 'Boolean',
					'description' => __( 'Include events that are shown on the calendar grid.', 'ql-events' ),
				],
				'timezone'       => [
					'type'        => 'String',
					'description' => __( 'Filter events by timezone.', 'ql-events' ),
				],
				'hiddenFromUpcoming'       => [
					'type'        => 'Boolean',
					'description' => __( 'Include events hidden from the upcoming list', 'ql-events' ),
				],
				'sticky'       => [
					'type'        => 'Boolean',
					'description' => __( 'Include sticky events', 'ql-events' ),
				],
				'featured'       => [
					'type'        => 'Boolean',
					'description' => __( 'Include featured events', 'ql-events' ),
				],
				'hidden'       => [
					'type'        => 'Boolean',
					'description' => __( 'Include hidden events', 'ql-events' ),
				],
				'organizer'    => [
					'type'        => 'ID',
					'description' => __( 'Filter events by organizer.', 'ql-events' ),
				],
				'venuesIn'             => [
					'type'        => [ 'list_of' => 'Int' ],
					'description' => __( 'Filter the connection based on event venue ID', 'ql-events' ),
				],
				'venuesNotIn'          => [
					'type'        => [ 'list_of' => 'Int' ],
					'description' => __( 'Filter the connection based on event venue ID', 'ql-events' ),
				],
				'startDateQuery'       => [
					'type'              => 'DateQueryInput',
					'description'       => __( 'Filter the connection based on event start dates', 'ql-events' ),
					'deprecationReason' => __( 'Deprecated in favor of using the "startDate"', 'ql-events' ),
				],
				'endDateQuery'         => [
					'type'              => 'DateQueryInput',
					'description'       => __( 'Filter the connection based on event end dates', 'ql-events' ),
					'deprecationReason' => __( 'Deprecated in favor of using the "endDate"', 'ql-events' ),
				],
			],
			Main::POSTTYPE
		);

		return $connection_args;
	}
}
