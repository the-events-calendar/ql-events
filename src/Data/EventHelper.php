<?php
/**
 * Event Helper methods for the resolver Factory.
 *
 * @package WPGraphQL\TEC\Data
 * @since 0.0.1
 */

namespace WPGraphQL\TEC\Data;

use WPGraphQL\AppContext;
use GraphQL\Type\Definition\ResolveInfo;
use WP_Post;
use WP_Term;

/**
 * Class - Event Helper
 */
class EventHelper {
	/**
	 * Modifies the default connection configuration.
	 *
	 * @param array $config .
	 */
	public static function get_connection_config( array $config ) : array {
		$config['connectionArgs'] = array_merge(
			$config['connectionArgs'],
			self::get_connection_args(),
		);

		$config['resolve'] = function( $source, array $args, AppContext $context, ResolveInfo $info ) {
			$args = self::map_args( $args );

			return Factory::resolve_events_connection( $source, $args, $context, $info );
		};

		return $config;
	}

	/**
	 * Gets an array of connection args to register to the Event Query.
	 */
	public static function get_connection_args() : array {
		return [
			// Event Category.
			'eventCategoryId'    => [
				'type'        => 'Int',
				'description' => __( 'Category ID.', 'wp-graphql-tec' ),
			],
			'eventCategoryIn'    => [
				'type'        => [ 'list_of' => 'ID' ],
				'description' => __( 'Array of category IDs, used to display objects from one category OR another.', 'wp-graphql-tec' ),
			],
			'eventCategoryName'  => [
				'type'        => 'String',
				'description' => __( 'Use Event Category slug.', 'wp-graphql-tec' ),
			],
			'eventCategoryNotIn' => [
				'type'        => [ 'list_of' => 'ID' ],
				'description' => __( 'Array of category IDs, used to display objects from one category OR another.', 'wp-graphql-tec' ),
			],
			// Organizer.
			'organizerId'        => [
				'type'        => 'Int',
				'description' => __( 'Organizer ID.', 'wp-graphql-tec' ),
			],
			'organizerIn'        => [
				'type'        => [ 'list_of' => 'ID' ],
				'description' => __( 'Array of organizer IDs, used to display objects from one organizer OR another.', 'wp-graphql-tec' ),
			],
			'organizerName'      => [
				'type'        => 'String',
				'description' => __( 'Use Organizer slug.', 'wp-graphql-tec' ),
			],
			'organizerNotIn'     => [
				'type'        => [ 'list_of' => 'ID' ],
				'description' => __( 'Array of organizer IDs, used to display objects from one organizer OR another.', 'wp-graphql-tec' ),
			],
			// Venue.
			'venueId'            => [
				'type'        => 'Int',
				'description' => __( 'Venue ID.', 'wp-graphql-tec' ),
			],
			'venueIn'            => [
				'type'        => [ 'list_of' => 'ID' ],
				'description' => __( 'Array of venue IDs, used to display objects from one venue OR another.', 'wp-graphql-tec' ),
			],
			'venueName'          => [
				'type'        => 'String',
				'description' => __( 'Use Venue slug.', 'wp-graphql-tec' ),
			],
			'venueNotIn'         => [
				'type'        => [ 'list_of' => 'ID' ],
				'description' => __( 'Array of venue IDs, used to display objects from one venue OR another.', 'wp-graphql-tec' ),
			],
		];
	}

	/**
	 * Converts where arg keys to those understood by TEC.
	 *
	 * @param array $args The GraphQL query where args.
	 */
	public static function map_args( array $args ) : array {
		// Event Category Name to ID.
		if ( isset( $args['where']['eventCategoryName'] ) ) {
			$term = get_term_by( 'slug', $args['where']['eventCategoryName'], 'tribe_events_cat' );
			if ( $term instanceof WP_Term ) {
				$args['where']['eventCategoryId'] = $term->term_taxonomy_id;
			}
		}

		// Event Organizer Name to ID.
		if ( isset( $args['where']['organizerName'] ) ) {
			$post = get_page_by_path( $args['where']['organizerName'], OBJECT, 'tribe_organizer' );
			if ( $post instanceof WP_Post ) {
				$args['where']['organizerId'] = $post->ID;
			}
		}

		// Event Venue Name to ID.
		if ( isset( $args['where']['venueName'] ) ) {
			$post = get_page_by_path( $args['where']['venueName'], OBJECT, 'tribe_venue' );
			if ( $post instanceof WP_Post ) {
				$args['where']['venueId'] = $post->ID;
			}
		}

		return $args;
	}
}
