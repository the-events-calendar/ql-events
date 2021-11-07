<?php
/**
 * Connection - Organizers.
 *
 * Registers connections from other types to Organizers.
 *
 * @package \WPGraphQL\TEC\Connection
 * @since   0.0.1
 */

namespace WPGraphQL\TEC\Connection;

use GraphQL\Type\Definition\ResolveInfo;
use WPGraphQL\AppContext;
use WPGraphQL\Data\Connection\PostObjectConnectionResolver;
use WPGraphQL\TEC\Type\WPObject\Event;
use WPGraphQL\TEC\Type\WPObject\Organizer;

/**
 * Class - Organizers
 */
class Organizers {
		/**
		 * The GraphQL field name for the connection.
		 *
		 * @var string
		 */
	public static string $from_field_name = 'organizers';

	/**
	 * Registers the various connections from other Types to Organizers.
	 */
	public static function register_connections() : void {
		// From Event.
		register_graphql_connection(
			[
				'fromType'      => Event::$type,
				'toType'        => Organizer::$type,
				'fromFieldName' => self::$from_field_name,
				'resolve'       => function ( $source, array $args, AppContext $context, ResolveInfo $info ) {
					if ( empty( $source->organizerIds ) ) {
						return null;
					}
					$resolver = new PostObjectConnectionResolver( $source, $args, $context, $info );
					$resolver->set_query_arg( 'post__in', $source->organizerIds );

					return $resolver->get_connection();
				},
			]
		);
	}
}
