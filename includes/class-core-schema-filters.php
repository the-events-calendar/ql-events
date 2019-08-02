<?php
/**
 * Adds filters that modify core schema.
 *
 * @package \WPGraphQL\Extensions\QL_Events
 * @since   0.0.1
 */

namespace WPGraphQL\Extensions\QL_Events;

/**
 * Class Core_Schema_Filters
 */
class Core_Schema_Filters {

	/**
	 * Register filters
	 */
	public static function add_filters() {
		add_action( 'register_post_type_args', array( __CLASS__, 'register_post_types' ), 10, 2 );
		add_action( 'register_taxonomy_args', array( __CLASS__, 'register_taxonomies' ), 10, 2 );
	}

	/**
	 * Register TEC post types to GraphQL schema
	 *
	 * @param array  $args      - post-type args.
	 * @param string $post_type - post-type slug.
	 *
	 * @return array
	 */
	public static function register_post_types( $args, $post_type ) {
		if ( 'tribe_events' === $post_type ) {
			$args['show_in_graphql']     = true;
			$args['graphql_single_name'] = 'Event';
			$args['graphql_plural_name'] = 'Events';
		}

		if ( 'tribe_organizer' === $post_type ) {
			$args['show_in_graphql']     = true;
			$args['graphql_single_name'] = 'Organizer';
			$args['graphql_plural_name'] = 'Organizers';
		}

		if ( 'tribe_venue' === $post_type ) {
			$args['show_in_graphql']     = true;
			$args['graphql_single_name'] = 'Venue';
			$args['graphql_plural_name'] = 'Venues';
		}

		return $args;
	}

	/**
	 * Register TEC taxonomies to GraphQL schema
	 *
	 * @param array  $args     - taxonomy args.
	 * @param string $taxonomy - taxonomy slug.
	 *
	 * @return array
	 */
	public static function register_taxonomies( $args, $taxonomy ) {
		if ( 'tribe_events_cat' === $taxonomy ) {
			$args['show_in_graphql']     = true;
			$args['graphql_single_name'] = 'EventsCategory';
			$args['graphql_plural_name'] = 'EventsCategories';
		}

		return $args;
	}
}
