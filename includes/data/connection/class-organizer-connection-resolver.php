<?php
/**
 * Connection resolver - Organizers
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
 * Class Organizer_Connection_Resolver
 */
class Organizer_Connection_Resolver {
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
		// Determine where we're at in the Graph and adjust the query context appropriately.
		if ( true === is_object( $source ) && Main::POSTTYPE === $source->post_type ) {
			// @codingStandardsIgnoreLine
			if ( 'organizers' === $info->fieldName ) {
				$query_args['post_parent'] = 0;
				$query_args['post__in']    = get_post_meta( $source->ID, '_EventOrganizerID' );
			}
		}

		$query_args = apply_filters(
			'ql_events_' . Main::ORGANIZER_POST_TYPE . '_connection_query_args',
			$query_args,
			$source,
			$args,
			$context,
			$info
		);

		return $query_args;
	}
}
