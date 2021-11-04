<?php
/**
 * Connection - Events.
 *
 * Registers connections from other types to Events.
 *
 * @package \WPGraphQL\TEC\Connection
 * @since   0.0.1
 */

namespace WPGraphQL\TEC\Connection;

use GraphQL\Type\Definition\ResolveInfo;
use WPGraphQL\AppContext;
use WPGraphQL\Connection\PostObjects;
use WPGraphQL\TEC\Data\Connection\EventConnectionResolver;
use WPGraphQL\TEC\Type\WPObject\Event;
use WPGraphQL\TEC\Type\WPObject\Organizer;
use WPGraphQL\TEC\Type\WPObject\Venue;


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
		// From organizers.
		register_graphql_connection(
			[
				'fromType'      => Organizer::$type,
				'toType'        => Event::$type,
				'fromFieldName' => self::$from_field_name,
				'resolve'       => function ( $source, array $args, AppContext $context, ResolveInfo $info ) {
					$resolver = new EventConnectionResolver( $source, $args, $context, $info );

					$resolver->set_query_arg( 'organizer', $source->database_id );

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

					$resolver->set_query_arg( 'venue', $source->database_id );

					return $resolver->get_connection();
				},
			]
		);
	}

	/**
	 * Gets the connection args for the Root Query Connection.
	 *
	 * @return array
	 */
	public static function get_connection_args() : array {
		return array_merge(
			PostObjects::get_connection_args(),
			[]
		);
	}
}
