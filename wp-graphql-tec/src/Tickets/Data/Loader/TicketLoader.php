<?php
/**
 * DataLoader - TicketLoader
 *
 * Loads Models for TEC Ticket post type.
 *
 * @package WPGraphQL\TEC\Tickets\Data\Loader
 * @since 0.0.1
 */

namespace WPGraphQL\TEC\Tickets\Data\Loader;

use GraphQL\Error\UserError;
use WP_Query;
use WPGraphQL\Data\Loader\AbstractDataLoader;
use WPGraphQL\TEC\Tickets\Model\RsvpTicket;
use WPGraphQL\TEC\Tickets\Model\Ticket;
use WPGraphQL\TEC\Tickets\Model\PurchasableTicket;
use WPGraphQL\TEC\Utils\Utils;
use WP_Post;
/**
 * Class - TicketLoader
 */
class TicketLoader extends AbstractDataLoader {
	/**
	 * {@inheritDoc}
	 *
	 * @param WP_Post $entry .
	 */
	protected function get_model( $entry, $key ) : ?Ticket {
		if (
			empty( $entry->post_type ) ||
			! in_array( $entry->post_type, [ ...array_keys( Utils::get_et_ticket_types() ), 'revision' ], true )
		) {
			return null;
		}

		/**
		 * If there's a Post Author connected to the post, we need to resolve the
		 * user as it gets set in the globals via `setup_post_data()` and doing it this way
		 * will batch the loading so when `setup_post_data()` is called the user
		 * is already in the cache.
		 */
		$context     = $this->context;
		$post_parent = null;

		if ( 'revision' === $entry->post_type && ! empty( $entry->post_parent ) && absint( $entry->post_parent ) ) {
			$post_parent = $entry->post_parent;
			$context->get_loader( 'ticket' )->load_deferred( $post_parent );
		}

		$post = null;
		switch ( $entry->post_type ) {
			case 'tribe_rsvp_tickets':
				$post = new RsvpTicket( $entry );
				break;
			case 'tec_tc_ticket':
			case 'tribe_tpp_ticket':
				$post = new PurchasableTicket( $entry );
				break;
			default:
				$post = new Ticket( $entry );
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

		$post_types = array_keys( Utils::get_et_ticket_types() );

		$args = [
			'post_types'          => $post_types,
			'post_status'         => 'any',
			'posts_per_page'      => count( $keys ),
			'post__in'            => $keys,
			'orderby'             => 'post__in',
			'no_found_rows'       => true,
			'split_the_query'     => false,
			'ignore_sticky_posts' => true,
		];

		/**
		 * Ensure that WP_Query doesn't first ask for IDs since we already have them.
		 */
		add_filter(
			'split_the_query',
			function ( $split, WP_Query $query ) {
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
		new WP_Query( $args );

		$loaded_posts = [];
		foreach ( $keys as $key ) {
			/**
			 * The query above has added our objects to the cache
			 * so now we can pluck them from the cache to return here
			 * and if they don't exist or aren't a valid post-type we can throw an error, otherwise
			 * we can proceed to resolve the object via the Model layer.
			 */
			$post_type = get_post_type( $key );
			if ( ! $post_type ) {
				$loaded_posts[ $key ] = null;
			}

			if ( ! in_array( $post_type, $post_types, true ) ) {
				/* translators: invalid post-type error message */
				throw new UserError( sprintf( __( '%s is not a valid Ticket post type', 'wp-graphql-tec' ), $post_type ) );
			}
			$loaded_posts[ $key ] = get_post( (int) $key );
		}

		return $loaded_posts;
	}
}
