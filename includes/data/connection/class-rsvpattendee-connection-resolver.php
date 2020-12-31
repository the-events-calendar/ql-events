<?php
/**
 * Connection resolver - RSVPAttendee
 *
 * Filters connections to RSVPAttendee type
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
 * Class RSVPAttendee_Connection_Resolver
 */
class RSVPAttendee_Connection_Resolver {
	/**
	 * This prepares the $query_args for use in the connection query. This is where default $args are set, where dynamic
	 * $args from the $this->source get set, and where mapping the input $args to the actual $query_args occurs.
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

		/**
		 * Collect the input_fields and sanitize them to prepare them for sending to the WP_Query
		 */
		$input_fields = array();
		if ( ! empty( $args['where'] ) ) {
			$input_fields = self::sanitize_input_fields( $args['where'] );
		}

		if ( ! empty( $input_fields ) ) {
			$query_args = array_merge( $query_args, $input_fields );
		}

		// Determine where we're at in the Graph and adjust the query context appropriately.
		// @Todo: this was left over from class-attendee-connection-resolver.php which wasnt loaded. May not be necessary.
		if ( true === is_object( $source ) ) {


			switch ( $source->post_type ) {
				case Main::POSTTYPE:
					// @codingStandardsIgnoreLine
					if ( 'rsvpAttendees' === $info->fieldName ) {
						if ( ! isset( $query_args['meta_query'] ) ) {
							$query_args['meta_query'] = array(); // WPCS: slow query ok.
						}
						$query_args['meta_query'][] = array(
							'key'     => RSVP::ATTENDEE_EVENT_KEY,
							'value'   => $source->ID,
							'compare' => '=',
						);
					}
					break;
			}
			// @codingStandardsIgnoreLine
			
		}

		$query_args = apply_filters(
			'graphql_rsvp_attendee_connection_query_args',
			$query_args,
			$source,
			$args,
			$context,
			$info
		);

		return $query_args;
	}

		/**
	 * This sets up the "allowed" args, and translates the GraphQL-friendly keys to WP_Query
	 * friendly keys. There's probably a cleaner/more dynamic way to approach this, but
	 * this was quick. I'd be down to explore more dynamic ways to map this, but for
	 * now this gets the job done.
	 *
	 * @since  0.0.5
	 * @access private
	 *
	 * @param array $args  Where argument input.
	 *
	 * @return array
	 */

	private static function sanitize_input_fields( $args ){
		$query_args = array();

		if ( ! empty( $args['eventsIn'])){
			$query_args['meta_query'] = array(); // WPCS: slow query ok.
			$query_args['meta_query'][] = array(
				'key'     => RSVP::ATTENDEE_EVENT_KEY,
				'value'   => $args['eventsIn'],
				'compare' => 'IN',
			);
		}

		return $query_args;
	}
}
