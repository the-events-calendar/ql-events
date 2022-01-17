<?php
/**
 * Factory Class
 *
 * This class serves as a factory for all TEC resolvers.
 *
 * @package WPGraphQL\TEC\Events\Data
 * @since 0.0.1
 */

namespace WPGraphQL\TEC\Events\Data;

use WP_Post;
use WPGraphQL\AppContext;
use WPGraphQL\Model\Model as GraphQLModel;
use WPGraphQL\TEC\Common\Type\WPInterface\NodeWithJsonLd;
use WPGraphQL\TEC\Events\Model;
use WPGraphQL\TEC\Events\Type\WPInterface\NodeWithOrganizers;
use WPGraphQL\TEC\Events\Type\WPInterface\NodeWithVenue;
use WPGraphQL\TEC\Events\Type\WPObject;
use WPGraphQL\TEC\Traits\PostTypeResolverMethod;
use WPGraphQL\TEC\Utils\Utils;

/**
 * Class - Factory
 */
class Factory {
	use PostTypeResolverMethod;

	/**
	 * Ensures the correct models are used even if the dataloader is wrong.
	 *
	 * @param null  $model  Possible model instance to be loader.
	 * @param mixed $entry  Data source.
	 * @return GraphQLModel|null
	 */
	public static function set_models_for_dataloaders( $model, $entry ) {
		if ( is_a( $entry, WP_Post::class ) ) {
			switch ( $entry->post_type ) {
				case 'tribe_events':
					$model = new Model\Event( $entry );
					break;
				case 'tribe_organizer':
					$model = new Model\Organizer( $entry );
					break;
				case 'tribe_venue':
					$model = new Model\Venue( $entry );
					break;
			}
		}

		return $model;
	}

	/**
	 * Resolves Relay node for some TEC GraphQL types.
	 *
	 * @param mixed $type     Node type.
	 * @param mixed $node     Node object.
	 *
	 * @return mixed
	 */
	public static function resolve_node_type( $type, $node ) {
		switch ( true ) {
			case is_a( $node, Model\Event::class ):
				$type = Model\Event::class;
				break;
			case is_a( $node, Model\Organizer::class ):
				$type = Model\Organizer::class;
				break;
			case is_a( $node, Model\Venue::class ):
				$type = Model\Venue::class;
				break;
		}

		return $type;
	}

	/**
	 * Overwrites the GraphQL config for auto-registered object types.
	 *
	 * @param array $config .
	 */
	public static function set_object_type_config( array $config ) : array {
		$post_type = Utils::graphql_type_to_post_type( $config['name'] );

		if ( is_null( $post_type ) ) {
			return $config;
		}

		if ( 'tribe_events' === $post_type ) {
			$config['interfaces'] = array_merge(
				$config['interfaces'] ?? [],
				[
					NodeWithJsonLd::$type,
					NodeWithVenue::$type,
					NodeWithOrganizers::$type,
				]
			);

			$config['resolve'] = function( $source, array $args, AppContext $context ) use ( $post_type ) {
				return self::resolve( $post_type, $source, $args, $context );
			};
		}

		return $config;
	}

	/**
	 * Overwrites the GraphQL config for auto-registered object types.
	 *
	 * @param array $config .
	 */
	public static function set_connection_type_config( array $config ) : array {
			// Return early if not RootQuery or EventsCategory.
		if ( ! in_array( $config['fromType'], [ 'RootQuery', 'EventCategory' ], true ) ) {
			return $config;
		}

		switch ( $config['toType'] ) {
			case WPObject\Event::$type:
				$config = EventHelper::get_connection_config( $config );
				break;
			case WPObject\Organizer::$type:
				$config = OrganizerHelper::get_connection_config( $config );
				break;
			case WPObject\Venue::$type:
				$config = VenueHelper::get_connection_config( $config );
				break;
		}

		return $config;
	}
}
