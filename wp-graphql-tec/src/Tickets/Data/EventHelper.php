<?php
/**
 * Extends the EventHelper.
 *
 * @package WPGraphQL\TEC\Tickets\Data
 * @since 0.0.1
 */

namespace WPGraphQL\TEC\Tickets\Data;

/**
 * Class - Event Helper
 */
class EventHelper {

	/**
	 * Extends event connection with where args.
	 *
	 * @param array $connection_args .
	 */
	public static function add_where_args_to_events_connection( array $connection_args ) : array {
		return array_merge(
			$connection_args,
			[
				'costCurrencySymbol' => [
					'type'        => [ 'list_of' => 'String' ],
					'description' => __( 'One or more currency symbols or currency ISO codes.', 'wp-graphql-tec' ),
				],
				'hasTickets'         => [
					'type'        => 'Boolean',
					'description' => __( 'Filters events that either have or dont have tickets, based on the provided state. This does NOT include RSVPs or events that have a cost assigned via the cost custom field', 'wp-graphql-tec' ),
				],
				'hasRsvp'            => [
					'type'        => 'Boolean',
					'description' => __( 'Filters events that either have or dont have RSVP tickets, based on the provided state.', 'wp-graphql-tec' ),
				],
				'hasRsvpOrTickets'   => [
					'type'        => 'Boolean',
					'description' => __( 'Filters events that either have or dont have either RSVP or regular tickets, based on the provided state.', 'wp-graphql-tec' ),
				],
			]
		);
	}

}
