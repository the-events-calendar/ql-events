<?php
/**
 * Factory Class
 *
 * This class serves as a factory for all resolvers.
 *
 * @package WPGraphQL\TEC\Data
 * @since 0.0.1
 */

namespace WPGraphQL\TEC\Data;

use GraphQL\Deferred;
use GraphQL\Error\UserError;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQLRelay\Relay;
use WP_Post;
use WPGraphQL\AppContext;
use WPGraphQL\Model\Model;
use WPGraphQL\Registry\TypeRegistry;
use WPGraphQL\TEC\Data\Connection\EventConnectionResolver;
use WPGraphQL\TEC\Data\Connection\OrganizerConnectionResolver;
use WPGraphQL\TEC\Data\Connection\VenueConnectionResolver;
use WPGraphQL\TEC\Model\Event;
use WPGraphQL\TEC\Model\Organizer;
use WPGraphQL\TEC\Model\Venue;
use WPGraphQL\TEC\Utils\Utils;
use WPGraphQL\Type\WPObjectType;


/**
 * Class - Factory
 */
class Factory {

	/**
	 * Returns an event object.
	 *
	 * @param int        $id      Group ID.
	 * @param AppContext $context AppContext object.
	 * @return Deferred|null
	 */
	public static function resolve_event_object( $id, AppContext $context ) :?Deferred {
		if ( empty( $id ) ) {
			return null;
		}
		$event_id = absint( $id );

		return new Deferred(
			function () use ( $event_id, $context ) {
				return $context->get_loader( 'tribe_events' )->load( $event_id );
			}
		);
	}

	/**
	 * Returns an organizer object.
	 *
	 * @param int        $id      Group ID.
	 * @param AppContext $context AppContext object.
	 * @return Deferred|null
	 */
	public static function resolve_organizer_object( $id, AppContext $context ) :?Deferred {
		if ( empty( $id ) ) {
			return null;
		}
		$organizer_id = absint( $id );

		return new Deferred(
			function () use ( $organizer_id, $context ) {
				return $context->get_loader( 'tribe_organizer' )->load( $organizer_id );
			}
		);
	}
	/**
	 * Returns an venue object.
	 *
	 * @param int        $id      Group ID.
	 * @param AppContext $context AppContext object.
	 * @return Deferred|null
	 */
	public static function resolve_venue_object( $id, AppContext $context ) :?Deferred {
		if ( empty( $id ) ) {
			return null;
		}
		$venue_id = absint( $id );

		return new Deferred(
			function () use ( $venue_id, $context ) {
				return $context->get_loader( 'tribe_venue' )->load( $venue_id );
			}
		);
	}

	/**
	 * Wrapper for the EventConnectionResolver class.
	 *
	 * @param mixed       $source  Source.
	 * @param array       $args    Query args to pass to the connection resolver.
	 * @param AppContext  $context The context of the query to pass along.
	 * @param ResolveInfo $info    The ResolveInfo object.
	 * @return Deferred
	 */
	public static function resolve_events_connection( $source, array $args, AppContext $context, ResolveInfo $info ): Deferred {
		error_log('resolving');
		return ( new EventConnectionResolver( $source, $args, $context, $info ) )->get_connection();
	}

	/**
	 * Wrapper for the OrganizerConnectionResolver class.
	 *
	 * @param mixed       $source  Source.
	 * @param array       $args    Query args to pass to the connection resolver.
	 * @param AppContext  $context The context of the query to pass along.
	 * @param ResolveInfo $info    The ResolveInfo object.
	 * @return Deferred
	 */
	public static function resolve_organizers_connection( $source, array $args, AppContext $context, ResolveInfo $info ): Deferred {
		return ( new OrganizerConnectionResolver( $source, $args, $context, $info ) )->get_connection();
	}

	/**
	 * Wrapper for the VenueConnectionResolver class.
	 *
	 * @param mixed       $source  Source.
	 * @param array       $args    Query args to pass to the connection resolver.
	 * @param AppContext  $context The context of the query to pass along.
	 * @param ResolveInfo $info    The ResolveInfo object.
	 * @return Deferred
	 */
	public static function resolve_venues_connection( $source, array $args, AppContext $context, ResolveInfo $info ): Deferred {
		return ( new VenueConnectionResolver( $source, $args, $context, $info ) )->get_connection();
	}


	/**
	 * Resolves Relay node for some WooGraphQL types.
	 *
	 * @param mixed $type     Node type.
	 * @param mixed $node     Node object.
	 *
	 * @return mixed
	 */
	public static function resolve_node_type( $type, $node ) {
		switch ( true ) {
			case is_a( $node, Event::class ):
				$type = Event::class;
				break;
			case is_a( $node, Organizer::class ):
				$type = Organizer::class;
				break;
			case is_a( $node, Venue::class ):
				$type = Venue::class;
				break;
		}

		return $type;
	}

	/**
	 * Ensures the correct models are used even if the dataloader is wrong.
	 *
	 * @param null  $model  Possible model instance to be loader.
	 * @param mixed $entry  Data source.
	 * @return Model|null
	 */
	public static function set_models_for_dataloaders( $model, $entry ) {
		if ( is_a( $entry, WP_Post::class ) ) {
			switch ( $entry->post_type ) {
				case 'tribe_events':
					$model = new Event( $entry );
					break;
				case 'tribe_organizer':
					$model = new Organizer( $entry );
					break;
				case 'tribe_venue':
					$model = new Venue( $entry );
					break;
			}
		}

		return $model;
	}

	/**
	 * Sets the resolver functions to the PostType registered by WPGraphQL.
	 *
	 * @param array $config .
	 */
	public static function register_post_resolvers( array $config ) : array {
		$post_type = Utils::graphql_type_to_post_type( $config['name'] );
		if ( is_null( $post_type ) ) {
			return $config;
		}

		$config['resolve'] = function( $source, array $args, AppContext $context ) use ( $post_type ) {
			return self::resolve( $post_type, $source, $args, $context );
		};

		return $config;
	}

	/**
	 * Sets the connection resolver functions to the PostType registered by WPGraphQL.
	 *
	 * @param array $config .
	 */
	public static function register_connection_resolvers( array $config ) : array {
		if ( ! isset( $config['connection_config']['toType'] ) || ! in_array( $config['connection_config']['toType'], array_keys( Utils::get_registered_post_types() ), true ) ) {
			return $config;
		}

		$type_name = lcfirst( $config['connection_config']['fromFieldName'] );

		$config['connection_config']['resolve'] = function( $source, array $args, AppContext $context, $info ) use ( $type_name ) : array {
			$const = call_user_func( [ __CLASS__, 'resolve_events_connection' ], $source, $args, $context, $info );
			return $const;
		};


		return $config;
	}

	/**
	 * Replaces the resolver function
	 *
	 * @param string     $post_type .
	 * @param mixed      $source .
	 * @param array      $args .
	 * @param AppContext $context .
	 * @return callable
	 *
	 * @throws UserError If no ID.
	 */
	public static function resolve( string $post_type, $source, array $args, AppContext $context ) {
			$idType  = $args['idType'] ?? 'global_id';
			$post_id = null;

		switch ( $idType ) {
			case 'slug':
				return $context->node_resolver->resolve_uri(
					$args['id'],
					[
						'name'      => $args['id'],
						'post_type' => $post_type,
					]
				);
			// @todo TEC venues and organizers are not public.
			case 'uri':
				return $context->node_resolver->resolve_uri(
					$args['id'],
					[
						'post_type' => $post_type,
						'archive'   => false,
						'nodeType'  => 'Page',
					]
				);
			case 'database_id':
				$post_id = absint( $args['id'] );
				break;
			case 'global_id':
			default:
				$id_components = Relay::fromGlobalId( $args['id'] );
				if ( ! isset( $id_components['id'] ) || ! absint( $id_components['id'] ) ) {
					throw new UserError( __( 'The ID input is invalid. Make sure you set the proper `idType` for your input.', 'wp-graphql-tec' ) );
				}
				$post_id = absint( $id_components['id'] );
				break;
		}

		if ( isset( $args['asPreview'] ) && true === $args['asPreview'] ) {
			$revisions = wp_get_post_revisions(
				$post_id,
				[
					'posts_per_page' => 1,
					'fields'         => 'ids',
					'check_enabled'  => false,
				]
			);
			$post_id   = ! empty( $revisions ) ? array_values( $revisions )[0] : null;
		}

			return absint( $post_id ) ? $context->get_loader( $post_type )->load_deferred( $post_id )->then(
				function ( $post ) use ( $post_type ) {
					if ( ! isset( $post->post_type ) || ! in_array(
						$post->post_type,
						[
							'revision',
							$post_type,
						],
						true
					) ) {
						return null;
					}
					return $post;
				}
			) : null;
	}
}
