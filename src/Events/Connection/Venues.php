<?php
/**
 * Connection - Venues.
 *
 * Registers connections from other types to Venues.
 *
 * @package \WPGraphQL\TEC\Events\Connection
 * @since   0.0.1
 */

namespace WPGraphQL\TEC\Events\Connection;

use GraphQL\Type\Definition\ResolveInfo;
use WPGraphQL\AppContext;
use WPGraphQL\Connection\PostObjects;
use WPGraphQL\Data\Connection\PostObjectConnectionResolver;
use WPGraphQL\TEC\Events\Data\Connection\VenueConnectionResolver;
use WPGraphQL\TEC\Events\Type\WPObject\Event;
use WPGraphQL\TEC\Events\Type\WPObject\Venue;

/**
 * Class - Venues
 */
class Venues extends PostObjects {
	/**
	 * The GraphQL field name for the connection.
	 *
	 * @var string
	 */
	public static string $from_field_name = 'venue';

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
				'oneToOne'      => true,
				'resolve'       => function ( $source, array $args, AppContext $context, ResolveInfo $info ) {
					$resolver = new PostObjectConnectionResolver( $source, $args, $context, $info );

					$resolver->set_query_arg( 'p', $source->venueId );

					return $resolver->one_to_one()->get_connection();
				},
			]
		);
	}
}
