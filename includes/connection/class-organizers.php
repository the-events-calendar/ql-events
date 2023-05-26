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
use WPGraphQL\Type\Connection\PostObjects;

/**
 * Class - Organizers
 */
class Organizers extends PostObjects {
	/**
	 * Registers the various connections from other Types to Organizers
	 *
	 * @since 0.0.1
	 *
	 * @return void
	 */
	public static function register_connections() {
		// From Event to Organizers.
		register_graphql_connection(
			self::get_connection_config(
				get_post_type_object( Main::ORGANIZER_POST_TYPE ),
				[
					'fromType'      => 'Event',
					'toType'        => 'Organizer',
					'fromFieldName' => 'organizers',
				]
			)
		);
	}
}
