<?php
/**
 * DataLoader - VenueLoader
 *
 * Loads Models for TEC Venue post type.
 *
 * @package WPGraphQL\TEC\Data\Loader
 * @since 0.0.1
 */

namespace WPGraphQL\TEC\Data\Loader;

use WP_Post;
use GraphQL\Error\UserError;
use WPGraphQL\Data\Loader\AbstractDataLoader;
use WPGraphQL\TEC\Model\Venue;

/**
 * Class - VenueLoader
 */
class VenueLoader extends AbstractDataLoader {
	/**
	 * {@inheritDoc}
	 */
	protected function get_model( $entry, $key ) : ?Venue {
		if ( ! $entry instanceof WP_Post || ! in_array( $entry->post_type, [ 'tribe_venue', 'revision' ], true ) ) {
			return null;
		}

		/**
		 * If there's a Post Author connected to the post, we need to resolve the
		 * user as it gets set in the globals via `setup_post_data()` and doing it this way
		 * will batch the loading so when `setup_post_data()` is called the user
		 * is already in the cache.
		 */
		$context     = $this->context;
		$user_id     = null;
		$post_parent = null;

		if ( ! empty( $entry->post_author ) && absint( $entry->post_author ) ) {
			if ( ! empty( $entry->post_author ) ) {
				$user_id = $entry->post_author;
				$context->get_loader( 'user' )->load_deferred( $user_id );
			}
		}

		if ( 'revision' === $entry->post_type && ! empty( $entry->post_parent ) && absint( $entry->post_parent ) ) {
			$post_parent = $entry->post_parent;
			$context->get_loader( 'tribe_venue' )->load_deferred( $post_parent );
		}

		$post = new Venue( $entry );
		if ( ! isset( $post->fields ) || empty( $post->fields ) ) {
			return null;
		}

		return $post;
	}

	/**
	 * {@inheritDoc}
	 *
	 * @throws UserError
	 */
	public function loadKeys( array $keys ) {
		if ( empty( $keys ) ) {
			return $keys;
		}

		/**
		 * Ensure that WP_Query doesn't first ask for IDs since we already have them.
		 */
		add_filter(
			'split_the_query',
			function ( $split, \WP_Query $query ) {
				if ( false === $query->get( 'split_the_query' ) ) {
					return false;
				}

				return $split;
			},
			10,
			2
		);

		/**
		 * We're provided a specific
		 * set of IDs, so we want to query as efficiently as possible with
		 * as little overhead as possible. We don't want to return post counts,
		 * we don't want to include sticky posts, and we want to limit the query
		 * to the count of the keys provided. The query must also return results
		 * in the same order the keys were provided in.
		 */
		tribe_venues()
			->where( 'post_status', 'any' )
			->where( 'posts_per_page', count( $keys ) )
			->where( 'post__in', $keys )
			->where( 'orderby', 'post__in' )
			->where( 'no_found_rows', true )
			->where( 'split_the_query', false )
			->where( 'ignore_sticky_posts', 'true' )
			->get_query();

		$loaded_posts = [];
		foreach ( $keys as $key ) {
			/**
			 * The query above has added our objects to the cache
			 * so now we can pluck them from the cache to return here
			 * and if they don't exist or aren't a valid post-type we can throw an error, otherwise
			 * we can proceed to resolve the object via the Model layer.
			 */
			$post_object = get_post( (int) $key );
			if ( ! $post_object instanceof WP_Post ) {
				$loaded_posts[ $key ] = null;
				continue;
			}

			if ( 'tribe_venue' !== $post_object->post_type ) {
				/* translators: invalid post-type error message */
				throw new UserError( sprintf( __( '%s is not a valid Venue post type', 'wp-graphql-tec' ), $post_object->post_type ) );
			}

			$loaded_posts[ $key ] = $post_object;
		}

		return $loaded_posts;
	}
}
