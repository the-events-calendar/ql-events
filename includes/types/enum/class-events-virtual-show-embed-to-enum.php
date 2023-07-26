<?php
/**
 * Enum Type - Events_Virtual_Show_Embed_To_Enum
 *
 * @package WPGraphQL\WooCommerce\Pro\Type\WPEnum
 * @since   0.3.0
 */

namespace WPGraphQL\QL_Events\Type\WPEnum;

/**
 * Class Events_Virtual_Show_Embed_At_Enum
 */
class Events_Virtual_Show_Embed_To_Enum {
	/**
	 * Registers type
	 *
	 * @since 0.3.0
	 *
	 * @return void
	 */
	public static function register() {
		register_graphql_enum_type(
			'EventsVirtualShowEmbedToEnum',
			[
				'description' => __( 'Triggers for when to display virtual event content.', 'ql-events' ),
				'values'      => [
					'EVERYONE'              => [ 'value' => 'all' ],
					'LOGGED_IN_USERS'       => [ 'value' => 'logged-in' ],
					'RSVP_ATTENDEES_ONLY'   => [ 'value' => 'rsvp' ],
					'TICKET_ATTENDEES_ONLY' => [ 'value' => 'ticket' ],
				],
			]
		);
	}
}
