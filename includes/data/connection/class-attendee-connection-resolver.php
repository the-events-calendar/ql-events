<?php
/**
 * Connection resolver - Attendee
 *
 * Filters connections to Attendee type
 *
 * @package WPGraphQL\QL_Events\Data\Connection
 * @since 0.0.1
 */

namespace WPGraphQL\QL_Events\Data\Connection;

use GraphQL\Type\Definition\ResolveInfo;
use Tribe__Events__Main as Main;
use Tribe__Tickets__RSVP as RSVP;
use WPGraphQL\AppContext;
use WPGraphQL\Model\Post;

/**
 * Class Attendee_Connection_Resolver
 */
class Attendee_Connection_Resolver {
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
		$rsvp = RSVP::get_instance();
		// Determine where we're at in the Graph and adjust the query context appropriately.
		if ( true === is_object( $source ) ) {
			switch ( $source->post_type ) {
				case Main::POSTTYPE:
					// @codingStandardsIgnoreLine
					if ( 'attendees' === $info->fieldName ) {
						if ( ! isset( $query_args['meta_query'] ) ) {
							$query_args['meta_query'] = []; // phpcs:ignore slow query ok.
						}
						$query_args['meta_query'][] = [
							'key'     => RSVP::ATTENDEE_EVENT_KEY,
							'value'   => $source->ID,
							'compare' => '=',
						];
					}
					break;
			}
			// @codingStandardsIgnoreLine

		}

		$query_args = apply_filters(
			'ql_events_rsvp_attendee_connection_query_args',
			$query_args,
			$source,
			$args,
			$context,
			$info
		);

		return $query_args;
	}
}
