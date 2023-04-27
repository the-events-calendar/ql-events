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
	 * Validate time input value.
	 *
	 * @since TBD
	 *
	 * @param string $input_field_name  Name of input field being validated.
	 * @param string $input_value       Input field value being validated.
	 *
	 * @throws UserError Invalid input.
	 *
	 * @return string
	 */
	private static function validate_time_input( $input_field_name, $input_value ) {
		$time = strtotime( $input_value );
		if ( false === $time ) {
			/* translators: Input field name. */
			throw new UserError( sprintf( __( 'Invalid time input provided for %s', 'ql-events' ), $input_field_name ) );
		}

		return $time;
	}

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
				'queryClass'     => Events_Query::class,
				'connectionArgs' => self::get_connection_args(),
				'resolve'        => function( $source, $args, $context, $info ) {
					$resolver = new PostObjectConnectionResolver( $source, $args, $context, $info );
					if ( ! empty( $args['startsAfter'] ) ) {
						$time = self::validate_time_input( 'startsAfter', $args['startsAfter'] );
						$resolver->set_query_arg( 'starts_after', $time );
					}
					if ( ! empty( $args['startsBefore'] ) ) {
						$time = self::validate_time_input( 'startsBefore', $args['startsBefore'] );
						$resolver->set_query_arg( 'starts_before', $time );
					}
					if ( ! empty( $args['startDate'] ) ) {
						$time = self::validate_time_input( 'startDate', $args['startDate'] );
						$resolver->set_query_arg( 'start_date', $time );
					}
					if ( ! empty( $args['endsAfter'] ) ) {
						$time = self::validate_time_input( 'endsAfter', $args['endsAfter'] );
						$resolver->set_query_arg( 'ends_after', $time );
					}
					if ( ! empty( $args['endsBefore'] ) ) {
						$time = self::validate_time_input( 'endsBefore', $args['endsBefore'] );
						$resolver->set_query_arg( 'ends_before', $time );
					}
					if ( ! empty( $args['endDate'] ) ) {
						$time = self::validate_time_input( 'endDate', $args['endDate'] );
						$resolver->set_query_arg( 'end_date', $time );
					}

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
				'venuesIn'       => [
					'type'        => [ 'list_of' => 'Int' ],
					'description' => __( 'Filter the connection based on event venue ID', 'ql-events' ),
				],
				'venuesNotIn'    => [
					'type'        => [ 'list_of' => 'Int' ],
					'description' => __( 'Filter the connection based on event venue ID', 'ql-events' ),
				],
				'startDateQuery' => [
					'type'        => 'DateQueryInput',
					'description' => __( 'Filter the connection based on event start dates', 'ql-events' ),
				],
				'endDateQuery'   => [
					'type'        => 'DateQueryInput',
					'description' => __( 'Filter the connection based on event end dates', 'ql-events' ),
				],
				'startsAfter'    => [
					'type'        => 'String',
					'description' => __( 'Include events that start after.', 'ql-events' ),
				],
				'startsBefore'   => [
					'type'        => 'String',
					'description' => __( 'Include events that start before.', 'ql-events' ),
				],
				'startDate'      => [
					'type'        => 'String',
					'description' => __( 'Include events that start at.', 'ql-events' ),
				],
				'endsAfter'      => [
					'type'        => 'String',
					'description' => __( 'Include events that end after.', 'ql-events' ),
				],
				'endsBefore'     => [
					'type'        => 'String',
					'description' => __( 'Include events that end before.', 'ql-events' ),
				],
				'endDate'        => [
					'type'        => 'String',
					'description' => __( 'Include events that end at.', 'ql-events' ),
				],
			],
			Main::POSTTYPE
		);

		return $connection_args;
	}
}
