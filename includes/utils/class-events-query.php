<?php
/**
 * WP_Query clone for wrapping the Tribe__Events__Query::getEvents() function
 *
 * @package WPGraphQL\QL_Events\Utils
 * @since   TBD
 */

namespace WPGraphQL\QL_Events\Utils;

use Tribe__Events__Query as Query;

/**
 * Class Events_Query
 */
class Events_Query {
	/**
	 * Stores query results.
	 *
	 * @since TBD
	 *
	 * @var \WP_Query
	 */
	protected $query;

	/**
	 * Events_Query constructor.
	 *
	 * @param array $args  Query Arguments.
	 */
	public function __construct( $args = [] ) {
		add_action( 'tec_events_custom_tables_v1_custom_tables_query_pre_get_posts', [ $this, 'remove_redirect_posts_orderby' ] );
		add_action( 'graphql_post_object_cursor_meta_key', [ $this, 'filter_meta_keys' ], 10, 5 );

		$this->query = apply_filters( 'tribe_get_events', Query::getEvents( $args, true ), $args, true );
	}

	/**
	 * Magic method to re-map the isset check on the child class looking for properties when
	 * resolving the fields
	 *
	 * @since TBD
	 *
	 * @param string $key The name of the field you are trying to retrieve
	 *
	 * @return bool
	 */
	public function __isset( $key ) {
		return isset( $this->query->$key );
	}

	/**
	 * Pass thru for query instance.
	 *
	 * @since TBD
	 *
	 * @param string $name  WP_Query member name.
	 *
	 * @return mixed
	 */
	public function __get( $name ) {
		return $this->query->$name;
	}

	/**
	 * Forwards function calls to WP_Query instance.
	 *
	 * @since TBD
	 *
	 * @param string $method - function name.
	 * @param array  $args  - function call arguments.
	 *
	 * @return mixed
	 *
	 * @throws BadMethodCallException Method not found on WP_Query object.
	 */
	public function __call( $method, $args ) {
		if ( \is_callable( [ $this->query, $method ] ) ) {
			return $this->query->$method( ...$args );
		}

		$class = static::class;
		throw new BadMethodCallException( "Call to undefined method {$method} on the {$class}" );
	}

	/**
	 * Removes 'posts_orderby' filter for GraphQL requests.
	 *
	 * @since TBD
	 *
	 * @param \Custom_Table_Query $query  Query instance.
	 *
	 * @return void
	 */
	public function remove_redirect_posts_orderby( $query ) {
		remove_filter( 'posts_orderby', [ $query, 'redirect_posts_orderby' ], 200 );
	}


	/**
	 * Filters meta keys for GraphQL requests.
	 *
	 * @since TBD
	 *
	 * @param string $key       The meta key to use for cursor based pagination
	 * @param string $meta_key  The original meta key
	 * @param string $meta_type The meta type
	 * @param string $order     The order direction
	 * @param object $cursor      The PostObjectCursor instance
	 *
	 * @return string
	 */
	public function filter_meta_keys( $key, $meta_key, $meta_type, $order, $cursor ) {
		if ( '_EventStartDate' === $meta_key ) {
			return 'wp_tec_occurrences.start_date';
		}

		if ( '_EventEndDate' === $meta_key ) {
			return 'wp_tec_occurrences.end_date';
		}

		return $key;
	}
}
