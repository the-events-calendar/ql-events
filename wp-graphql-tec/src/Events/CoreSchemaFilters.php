<?php
/**
 * Adds filters that modify core schema.
 *
 * @package \WPGraphQL\TEC\Events
 * @since   0.0.1
 */

namespace WPGraphQL\TEC\Events;

use WPGraphQL\AppContext;
use WPGraphQL\TEC\Events\Data\Factory;
use WPGraphQL\TEC\Events\Data\Loader\EventLoader;
use WPGraphQL\TEC\Interfaces\Hookable;

/**
 * Class - CoreSchemaFilters
 */
class CoreSchemaFilters implements Hookable {
	/**
	 * {@inheritdoc}
	 */
	public static function register_hooks() : void {
		// Registers post types.
		add_action( 'register_post_type_args', [ __CLASS__, 'register_post_types' ], 10, 2 );

		// Registers taxonomies.
		add_action( 'register_taxonomy_args', [ __CLASS__, 'register_taxonomies' ], 10, 2 );

		// Add data-loaders to AppContext.
		add_filter( 'graphql_data_loaders', [ __CLASS__, 'register_data_loaders' ], 10, 2 );

		add_filter( 'graphql_dataloader_pre_get_model', [ Factory::class, 'set_models_for_dataloaders' ], 10, 2 );

		// Add node resolvers.
		add_filter( 'graphql_resolve_node_type', [ Factory::class, 'resolve_node_type' ], 10, 2 );

		// Overwrite default GraphQL configs.
		add_filter( 'graphql_wp_object_type_config', [ Factory::class, 'set_object_type_config' ] );
		add_filter( 'graphql_wp_connection_type_config', [ Factory::class, 'set_connection_type_config' ], );
	}

	/**
	 * Register TEC post types to GraphQL schema
	 *
	 * @param array  $args      - post-type args.
	 * @param string $post_type - post-type slug.
	 */
	public static function register_post_types( array $args, string $post_type ): array {
		switch ( $post_type ) {
			case 'tribe_events':
				$args['show_in_graphql']     = true;
				$args['graphql_single_name'] = 'Event';
				$args['graphql_plural_name'] = 'Events';
				break;
			case 'tribe_organizer':
				$args['show_in_graphql']     = true;
				$args['graphql_single_name'] = 'Organizer';
				$args['graphql_plural_name'] = 'Organizers';

				break;
			case 'tribe_venue':
				$args['show_in_graphql']     = true;
				$args['graphql_single_name'] = 'Venue';
				$args['graphql_plural_name'] = 'Venues';
				break;
		}

		return $args;
	}

	/**
	 * Register TEC taxonomies to GraphQL schema
	 *
	 * @param array  $args     - taxonomy args.
	 * @param string $taxonomy - taxonomy slug.
	 */
	public static function register_taxonomies( array $args, string $taxonomy ) : array {
		if ( 'tribe_events_cat' === $taxonomy ) {
			$args['show_in_graphql']     = true;
			$args['graphql_single_name'] = 'EventCategory';
			$args['graphql_plural_name'] = 'EventCategories';
		}

		return $args;
	}

	/**
	 * Registers data-loaders to be used when resolving TEC-related GraphQL types.
	 *
	 * @param array      $loaders - assigned loaders.
	 * @param AppContext $context - AppContext instance.
	 */
	public static function register_data_loaders( array $loaders, AppContext $context ) : array {
		$event_loader            = new EventLoader( $context );
		$loaders['tribe_events'] = &$event_loader;

		return $loaders;
	}
}
