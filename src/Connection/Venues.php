<?php
/**
 * Connection - Venues.
 *
 * Registers connections from other types to Venues.
 *
 * @package \WPGraphQL\TEC\Connection
 * @since   0.0.1
 */

namespace WPGraphQL\TEC\Connection;

use GraphQL\Type\Definition\ResolveInfo;
use WPGraphQL\AppContext;
use WPGraphQL\Connection\PostObjects;
use WPGraphQL\TEC\Data\Connection\VenueConnectionResolver;
use WPGraphQL\TEC\Type\WPObject\Event;
use WPGraphQL\TEC\Type\WPObject\Venue;

/**
 * Class - Venues
 */
class Venues extends PostObjects {
	/**
	 * The GraphQL field name for the connection.
	 *
	 * @var string
	 */
	public static string $from_field_name = 'venues';

	/**
	 * Registers the various connections from other Types to Venues.
	 */
	public static function register_connections() : void {
		// From Event.
		register_graphql_connection(
			[
				'fromType'      => Event::$type,
				'toType'        => Venue::$type,
				'fromFieldName' => self::$from_field_name,
				'resolve'       => function ( $source, array $args, AppContext $context, ResolveInfo $info ) {
					$resolver = new VenueConnectionResolver( $source, $args, $context, $info );

					$resolver->set_query_arg( 'post__in', $source->venueId );

					return $resolver->one_to_one()->get_connection();
				},
			]
		);
	}
}
