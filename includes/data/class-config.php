<?php

namespace WPGraphQL\QL_Events\Data;


/**
 * Class Config
 *
 * This class contains configurations for various data-related things, such as query filters for
 * cursor pagination.
 *
 * @package WPGraphQL\Data
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
		add_filter( 'posts_where', [ $this, 'graphql_wp_query_cursor_pagination_support' ], 99, 2 );
	}

	public function graphql_wp_query_cursor_pagination_stability( string $orderby, WP_Query $wp_query ) {

		if ( true !== is_graphql_request() ) {
			return $orderby;
		}


		return $orderby;
	}
}
