<?php
/**
 * Connection - Events.
 *
 * Registers connections from other types to Events.
 *
 * @package \WPGraphQL\TEC\Events\Connection
 * @since   0.0.1
 */

namespace WPGraphQL\TEC\Events\Connection;

use GraphQL\Type\Definition\ResolveInfo;
use WPGraphQL\AppContext;
use WPGraphQL\TEC\Events\Data\Connection\EventConnectionResolver;
use WPGraphQL\TEC\Events\Type\WPObject\Event;
use WPGraphQL\TEC\Events\Type\WPObject\Organizer;
use WPGraphQL\TEC\Events\Type\WPObject\Venue;


/**
 * Class - Events
 */
class Events {
	/**
	 * The GraphQL field name for the connection.
	 *
	 * @var string
	 */
	public static string $from_field_name = 'events';

	/**
	 * Registers the various connections from other Types to Events.
	 */
	public static function register_connections() : void {
		register_graphql_connection(
			[
				'fromType'      => Organizer::$type,
				'toType'        => Event::$type,
				'fromFieldName' => self::$from_field_name,
				'resolve'       => function ( $source, array $args, AppContext $context, ResolveInfo $info ) {
					$resolver = new EventConnectionResolver( $source, $args, $context, $info );

					$resolver->set_query_arg( 'organizer', $source->ID );

					return $resolver->get_connection();
				},
			]
		);

		// From organizers.
		register_graphql_connection(
			[
				'fromType'      => Venue::$type,
				'toType'        => Event::$type,
				'fromFieldName' => self::$from_field_name,
				'resolve'       => function ( $source, array $args, AppContext $context, ResolveInfo $info ) {
					$resolver = new EventConnectionResolver( $source, $args, $context, $info );

					$resolver->set_query_arg( 'venue', $source->ID );

					return $resolver->get_connection();
				},
			]
		);
	}
}
