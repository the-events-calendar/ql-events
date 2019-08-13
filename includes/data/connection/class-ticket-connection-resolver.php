<?php
/**
 * Connection resolver - Ticket
 *
 * Filters connections to Ticket types
 *
 * @package WPGraphQL\Extensions\QL_Events\Data\Connection
 * @since 0.0.1
 */

namespace WPGraphQL\Extensions\QL_Events\Data\Connection;

use GraphQL\Type\Definition\ResolveInfo;
use WPGraphQL\AppContext;
use WPGraphQL\Model\Post;

/**
 * Class Ticket_Connection_Resolver
 */
class Ticket_Connection_Resolver {
	/**
	 * This prepares the $query_args for use in the "rsvpTickets" and "paypalTickets" connection query. This is where default $args are set, where dynamic
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
	public static function get_ticket_args( $query_args, $source, $args, $context, $info ) {
		$rsvp   = tribe( 'tickets.rsvp' );
		$paypal = tribe( 'tickets.commerce.paypal' );

		// Determine where we're at in the Graph and adjust the query context appropriately.
		if ( true === is_object( $source ) ) {
			// @codingStandardsIgnoreLine
			switch ( $info->fieldName ) {
				case 'rsvpTickets':
					$event_key       = $rsvp::ATTENDEE_EVENT_KEY;
					$connection_type = 'rsvp_tickets';
					break;
				case 'paypalTickets':
					$event_key       = $paypal::ATTENDEE_EVENT_KEY;
					$connection_type = 'paypal_tickets';
					break;
				default:
					break;
			}

			if ( $event_key ) {
				if ( empty( $query_args['meta_query'] ) ) {
					$query_args['meta_query'] = array(); // WPCS: slow query OK.
				}
				$query_args['meta_query'][] = array(
					'key'     => $event_key,
					'value'   => $source->ID,
					'compare' => '=',
				);
			}
		}

		if ( $connection_type ) {
			$query_args = apply_filters(
				"graphql_{$connection_type}_connection_query_args",
				$query_args,
				$source,
				$args,
				$context,
				$info
			);
		}

		return $query_args;
	}

	/**
	 * This prepares the $query_args for use in the "wooTickets" connection query. This is where default $args are set, where dynamic
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
	public static function get_ticket_plus_args( $query_args, $source, $args, $context, $info ) {
		$woocommerce = tribe( 'tickets-plus.commerce.woo' );

		// Determine where we're at in the Graph and adjust the query context appropriately.
		if ( true === is_object( $source ) ) {
			// @codingStandardsIgnoreLine
			if ( 'wooTickets' === $info->fieldName ) {
				if ( empty( $query_args['meta_query'] ) ) {
					$query_args['meta_query'] = array(); // WPCS: slow query OK.
				}
				$query_args['meta_query'][] = array(
					'key'     => $woocommerce::ATTENDEE_EVENT_KEY,
					'value'   => $source->ID,
					'compare' => '=',
				);
			}
		}

		if ( $connection_type ) {
			$query_args = apply_filters(
				'graphql_wootickets_connection_query_args',
				$query_args,
				$source,
				$args,
				$context,
				$info
			);
		}

		return $query_args;
	}
}
