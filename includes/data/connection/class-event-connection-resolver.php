<?php
/**
 * Connection resolver - Events
 *
 * Filters connections to Organizer types
 *
 * @package WPGraphQL\QL_Events\Data\Connection
 * @since 0.0.1
 */

namespace WPGraphQL\QL_Events\Data\Connection;

use GraphQL\Type\Definition\ResolveInfo;
use Tribe__Events__Main as Main;
use WPGraphQL\AppContext;
use WPGraphQL\Model\Post;

/**
 * Class Event_Connection_Resolver
 */
class Event_Connection_Resolver {
	/**
	 * This prepares the $query_args for use in the connection query. This is where default $args are set, where dynamic
	 * $args from the $this->source get set, and where mapping the input $args to the actual $query_args occurs.
	 *
	 * @since 0.0.1
	 *
	 * @param array       $query_args - WP_Query args.
	 * @param mixed       $source     - Connection parent resolver.
	 * @param array       $args       - Connection arguments.
	 * @param AppContext  $context    - AppContext object.
	 * @param ResolveInfo $info       - ResolveInfo object.
	 *
	 * @return mixed
	 */
	public static function get_query_args( $query_args, $source, $args, $context, $info ) {
		$query_args['tribe_suppress_query_filters'] = true;
		unset( $query_args['perm'] );

		/**
		 * Collect the input_fields and sanitize them to prepare them for sending to the WP_Query
		 */
		if ( ! empty( $args['where'] ) ) {
			$query_args = array_merge(
				$query_args,
				self::sanitize_input_fields( $args['where'] )
			);
		}

		/**
		 * Merge the input_fields with the default query_args
		 */

		return apply_filters(
			'ql_events_' . Main::POSTTYPE . '_connection_query_args',
			$query_args,
			$source,
			$args,
			$context,
			$info
		);
	}

	/**
	 * This sets up the "allowed" args, and translates the GraphQL-friendly keys to WP_Query
	 * friendly keys. There's probably a cleaner/more dynamic way to approach this, but
	 * this was quick. I'd be down to explore more dynamic ways to map this, but for
	 * now this gets the job done.
	 *
	 * @since  0.0.1
	 *
	 * @param array $args  Where argument input.
	 *
	 * @return array
	 */
	private static function sanitize_input_fields( $args ) {
		$query_args = [];

		if ( ! empty( $args['startDateQuery'] ) ) {
			$query_args['meta_query']   = []; // phpcs:ignore slow query ok.
			$query_args['meta_query'][] = self::date_query_input_to_meta_query( $args['startDateQuery'], '_EventStartDate' );
		}

		if ( ! empty( $args['endDateQuery'] ) ) {
			if ( ! isset( $query_args['meta_query'] ) ) {
				$query_args['meta_query'] = []; // phpcs:ignore slow query ok.
			}
			$query_args['meta_query'][] = self::date_query_input_to_meta_query( $args['endDateQuery'], '_EventEndDate' );
		}

		if ( ! empty( $args['venuesIn'] ) ) {
			if ( ! isset( $query_args['meta_query'] ) ) {
				$query_args['meta_query'] = []; // phpcs:ignore slow query ok.
			}
			$query_args['meta_query'][] = [
				'key'     => '_EventVenueID',
				'value'   => $args['venuesIn'],
				'compare' => 'IN',
			];
		}

		if ( ! empty( $args['venuesNotIn'] ) ) {
			if ( ! isset( $query_args['meta_query'] ) ) {
				$query_args['meta_query'] = []; // phpcs:ignore slow query ok.
			}
			$query_args['meta_query'][] = [
				'key'     => '_EventVenueID',
				'value'   => $args['venuesNotIn'],
				'compare' => 'NOT IN',
			];
		}

		return $query_args;
	}

	/**
	 * Takes a DateQueryInput and returns a meta query array.
	 *
	 * @since 0.0.1
	 *
	 * @param array  $date_query_input  DateQueryInput.
	 * @param string $meta_key          Target date meta key.
	 *
	 * @return array
	 */
	public static function date_query_input_to_meta_query( $date_query_input, $meta_key ) {
		// Create date string.
		$year   = isset( $date_query_input['year'] );
		$month  = isset( $date_query_input['month'] );
		$day    = isset( $date_query_input['day'] );
		$hour   = isset( $date_query_input['hour'] );
		$minute = isset( $date_query_input['minute'] );
		$second = isset( $date_query_input['second'] );
		$week   = isset( $date_query_input['week'] );
		$after  = isset( $date_query_input['after'] );
		$before = isset( $date_query_input['before'] );

		switch ( true ) {
			case $year && $month && $day && $hour:
				$date  = sprintf(
					'%4d-%02d-%02d %02d',
					$date_query_input['year'],
					$date_query_input['month'],
					$date_query_input['day'],
					$date_query_input['hour']
				);
				$date .= $minute ? sprintf( ':%02d', $date_query_input['minute'] ) : ':00';
				$date .= $second ? sprintf( ':%02d', $date_query_input['second'] ) : ':00';
				$type  = 'DATETIME';
				break;
			case $year && $month && $day:
				$date = sprintf(
					'%4d-%02d-%02d',
					$date_query_input['year'],
					$date_query_input['month'],
					$date_query_input['day']
				);
				break;
			case $year && $month:
				$date = sprintf( '%4d-%02d', $date_query_input['year'], $date_query_input['month'] );
				break;
			case $year && $week:
				$date = sprintf( '%4dW%02d', $date_query_input['year'], $date_query_input['week'] );
				break;
			case $year:
				$date = sprintf( '%4d', $date_query_input['year'] );
				break;
			case $after:
				$date = isset( $date_query_input['after']['year'] )
					? sprintf( '%4d', $date_query_input['after']['year'] )
					: gmdate( 'Y' );

				$date .= isset( $date_query_input['after']['month'] )
					? sprintf( '-%02d', $date_query_input['after']['month'] )
					: '-' . gmdate( 'm' );

				$date .= isset( $date_query_input['after']['day'] )
					? sprintf( '-%02d', $date_query_input['after']['day'] )
					: '-' . gmdate( 'd' );

				$compare = '>';
				break;
			case $before:
				$date = isset( $date_query_input['before']['year'] )
					? sprintf( '%4d', $date_query_input['before']['year'] )
					: gmdate( 'Y' );

				$date .= isset( $date_query_input['before']['month'] )
					? sprintf( '-%02d', $date_query_input['before']['month'] )
					: '-' . gmdate( 'm' );

				$date .= isset( $date_query_input['before']['day'] )
					? sprintf( '-%02d', $date_query_input['before']['day'] )
					: '-' . gmdate( 'd' );

				$compare = '<';
				break;
			default:
				$date = gmdate( 'Y-m-d' );
		}

		// Get compare value.
		if ( isset( $date_query_input['compare'] ) ) {
			if ( 'after' === strtolower( $date_query_input['compare'] ) ) {
				$compare = '>';
			} elseif ( 'before' === strtolower( $date_query_input['compare'] ) ) {
				$compare = '<';
			} else {
				$compare = $date_query_input['compare'];
			}
		}

		return [
			'key'     => $meta_key,
			'value'   => $date,
			'compare' => isset( $compare ) ? $compare : '=',
			'type'    => isset( $type ) ? $type : 'DATE',
		];
	}
}
