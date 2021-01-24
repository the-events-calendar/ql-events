<?php
/**
 * Connection - Organizers
 *
 * Registers connections to Organizer
 *
 * @package WPGraphQL\QL_Events\Connection
 * @since   0.0.1
 */

namespace WPGraphQL\QL_Events\Connection;

use Tribe__Events__Main as Main;
use GraphQL\Type\Definition\ResolveInfo;
use WPGraphQL\AppContext;
use WPGraphQL\Connection\PostObjects;
use WPGraphQL\Data\Connection\PostObjectConnectionResolver;



/**
 * Class - Organizers
 */
class Organizers extends PostObjects {
	/**
	 * Registers the various connections from other Types to Organizers
	 */
	public static function register_connections() {
		// From Event to Organizers.
		register_graphql_connection(
			self::get_connection_config(
				get_post_type_object( Main::ORGANIZER_POST_TYPE ),
				array(
					'fromType'      => 'Event',
					'toType'        => 'Organizer',
					'fromFieldName' => 'organizers',
					'resolve'       => function( $source, array $args, AppContext $context, ResolveInfo $info ) {
						$organizer_ids = tribe_get_organizer_ids( $source->ID );
						$resolver = new PostObjectConnectionResolver( $source, $args, $context, $info, Main::ORGANIZER_POST_TYPE );

						return ! empty( $organizer_ids ) ? $resolver->get_connection() : null;
					}
				)
			)
		);
	}
}
