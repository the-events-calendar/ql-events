<?php

namespace WPGraphQL\QL_Events\Data;

use WP_Query;

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
		add_filter( 'posts_orderby', [ $this, 'patchup_orderby' ], 200, 2 );
	}

	public function patchup_orderby( string $orderby, WP_Query $wp_query ) {

		if ( true !== is_graphql_request() ) {
			return $orderby;
		}

		$orderby_fields = preg_split( '/(,|\s)/', $orderby );

		\codecept_debug( $orderby_fields );


		return $orderby;
	}
}
