<?php
/**
 * Connection - Organizers
 *
 * Registers connections to Organizer
 *
 * @package WPGraphQL\Extensions\QL_Events\Connection
 * @since   0.0.1
 */

namespace WPGraphQL\Extensions\QL_Events\Connection;

use Tribe__Events__Main as Main;
use WPGraphQL\Connection\PostObjects;

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
				)
			)
		);
	}
}
