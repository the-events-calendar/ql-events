<?php
/**
 * Config
 *
 * The class defines SQL specific changes
 * needed for TEC to works properly with WPGraphQL.
 *
 * @package WPGraphQL\QL_Events\Data
 * @since   0.1.0
 */

namespace WPGraphQL\QL_Events\Data;

use WP_Query;

/**
 * Class Config
 */
class Config {

	/**
	 * Config constructor.
	 */
	public function __construct() {
		/**
		 * Filter the WP_Query to support cursor based pagination where a post ID can be used
		 * as a point of comparison when slicing the results to return.
		 */
		add_filter( 'posts_orderby', [ $this, 'patchup_orderby' ], 200, 2 );
	}

	/**
	 * Filters orderby argument to remove duplicates from the query.
	 *
	 * @param string   $orderby   Orderby input.
	 * @param WP_Query $wp_query  Query object.
	 *
	 * @return string
	 */
	public function patchup_orderby( string $orderby, WP_Query $wp_query ) {
		global $wpdb;
		if ( true !== is_graphql_request() ) {
			return $orderby;
		}

		$orderby_fields = preg_split( '/(,|\s)/', $orderby );

		\codecept_debug( $orderby_fields );

		return $orderby;
	}
}
