<?php
/**
 * Connection resolver - Ticket
 *
 * Filters connections to Ticket types
 *
 * @package WPGraphQL\QL_Events\Data\Connection
 * @since 0.0.1
 */

namespace WPGraphQL\QL_Events\Data\Connection;

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
					$connection_type = 'rsvp_tickets';
					break;
				case 'paypalTickets':
					$connection_type = 'paypal_tickets';
					break;
				default:
					$connection_type = false;
					break;
			}
		}

		if ( $connection_type ) {
			$query_args['post_parent'] = $source->ID;
			$query_args                = apply_filters(
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
		// Determine where we're at in the Graph and adjust the query context appropriately.
		if ( $source instanceof Post ) {
			// @codingStandardsIgnoreLine
			if ( 'wooTickets' === $info->fieldName ) {
				$woocommerce = tribe( 'tickets-plus.commerce.woo' );

				if ( ! empty( $args['meta_query'] ) ) {
					$query_args['meta_query'] = array(); // WPCS: slow query ok.
				}
				$query_args['meta_query'][] = array(
					'key'   => $woocommerce->event_key,
					'value' => $source->ID,
					'type'  => 'NUMERIC',
				);

				unset( $query_args['perm'] );
			}
		}

		$query_args = apply_filters(
			'graphql_wootickets_connection_query_args',
			$query_args,
			$source,
			$args,
			$context,
			$info
		);

		return $query_args;
	}

	/**
	 * Prepares default product connection catalog visibility for this "wooTicket" connection.
	 *
	 * @param array       $default_visibility  Default catalog visibility tax query.
	 * @param array       $query_args          The args that will be passed to the WP_Query.
	 * @param mixed       $source              The source that's passed down the GraphQL queries.
	 * @param array       $args                The inputArgs on the field.
	 * @param AppContext  $context             The AppContext passed down the GraphQL tree.
	 * @param ResolveInfo $info                The ResolveInfo passed down the GraphQL tree.
	 *
	 * @return mixed
	 */
	public static function get_ticket_plus_default_visibility( $default_visibility, $query_args, $source, $args, $context, $info ) {
		if ( $source instanceof Post ) {
			// @codingStandardsIgnoreLine
			if ( 'wooTickets' === $info->fieldName ) {
				$default_visibility = array(
					'taxonomy' => 'product_visibility',
					'field'    => 'slug',
					'terms'    => array( 'exclude-from-catalog', 'exclude-from-search' ),
					'operator' => 'IN',
				);
			}
		}

		return $default_visibility;
	}
}
