<?php
/**
 * Trait for classes that need to set a custom post resolver.
 *
 * @package \WPGraphQL\TEC\Traits
 * @since 0.0.1
 */

namespace WPGraphQL\TEC\Traits;

use GraphQL\Error\UserError;
use GraphQLRelay\Relay;
use WPGraphQL\AppContext;

/**
 * Trait - PostTypeResolverMethod
 */
trait PostTypeResolverMethod {

	/**
	 * Replaces the post resolver function
	 *
	 * @param string     $post_type .
	 * @param mixed      $source .
	 * @param array      $args .
	 * @param AppContext $context .
	 *
	 * @throws UserError If no ID.
	 */
	public static function resolve( string $post_type, $source, array $args, AppContext $context ) : callable {
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
