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
use WPGraphQL\Model\Model as GraphQLModel;
use WPGraphQL\TEC\Data\Connection\EventConnectionResolver;
use WPGraphQL\TEC\Data\Connection\OrganizerConnectionResolver;
use WPGraphQL\TEC\Data\Connection\VenueConnectionResolver;
use WPGraphQL\TEC\Model;
use WPGraphQL\TEC\Type\WPInterface\PurchasableTicket;
use WPGraphQL\TEC\Type\WPInterface\Ticket;
use WPGraphQL\TEC\Utils\Utils;
use WPGraphQL\TEC\Type\WPObject;

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

		$context->get_loader( 'tribe_events' )->buffer( [ $id ] );

		return new Deferred(
			function () use ( $id, $context ) {
				return $context->get_loader( 'tribe_events' )->load( $id );
			}
		);
	}

	/**
	 * Returns a ticket object.
	 *
	 * @param int        $id      Group ID.
	 * @param AppContext $context AppContext object.
	 * @return Deferred|null
	 */
	public static function resolve_ticket_object( $id, AppContext $context ) :?Deferred {
		if ( empty( $id ) ) {
			return null;
		}

		$context->get_loader( 'ticket' )->buffer( [ $id ] );

		return new Deferred(
			function () use ( $id, $context ) {
				return $context->get_loader( 'ticket' )->load( $id );
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

		$context->get_loader( 'post' )->buffer( [ $id ] );

		return new Deferred(
			function () use ( $id, $context ) {
				return $context->get_loader( 'post' )->load( $id );
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

		$context->get_loader( 'post' )->buffer( [ $id ] );

		return new Deferred(
			function () use ( $id, $context ) {
				return $context->get_loader( 'post' )->load( $id );
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
	 * Sets the resolver functions to the PostType registered by WPGraphQL.
	 *
	 * @param array $config .
	 */
	public static function register_post_resolvers( array $config ) : array {
		$post_type = Utils::graphql_type_to_post_type( $config['name'] );

		if ( is_null( $post_type ) ) {
			return $config;
		}

		switch ( $post_type ) {
			case 'tribe_events':
				$config['resolve'] = function( $source, array $args, AppContext $context ) use ( $post_type ) {
					return self::resolve( $post_type, $source, $args, $context );
				};
				break;
			case 'tribe_rsvp_tickets':
				$config['interfaces'] = array_merge(
					$config['interfaces'],
					[
						Ticket::$type,
					]
				);
				break;
			case 'tec_tc_ticket':
			case 'tribe_tpp_tickets':
				$config['interfaces'] = array_merge( $config['interfaces'], [ PurchasableTicket::$type ] );
				break;
		}

		return $config;
	}

	/**
	 * Sets the connection resolver functions to the PostType registered by WPGraphQL.
	 *
	 * @param array $config .
	 */
	public static function register_connection_resolvers( array $config ) : array {
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

	/**
	 * Replaces the post resolver function
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
