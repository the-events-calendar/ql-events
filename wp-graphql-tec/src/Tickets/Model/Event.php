<?php
/**
 * Extends the Event Model class
 *
 * @package \WPGraphQL\TEC\Tickets\Model
 * @since   0.0.1
 */

namespace WPGraphQL\TEC\Tickets\Model;

use GraphQLRelay\Relay;
use WP_Post;
use Tribe__Tickets__Tickets;

/**
 * Class - Event
 */
class Event {
	/**
	 * Extends the WPGraphQL Model.
	 *
	 * @param array   $fields The fields registered to the model.
	 * @param WP_Post $data The model data.
	 */
	public static function extend( array $fields, WP_Post $data ) : array {
		$post_type   = $data->post_type;
		$database_id = (int) $data->ID;
		$provider    = Tribe__Tickets__Tickets::get_event_ticket_provider_object( $database_id );

		$ticket_ids = ! empty( $provider ) ? $provider->get_tickets_ids( $database_id ) : null;

			$fields['availableTickets']         = function() use ( $database_id ) : int {
				return tribe_events_count_available_tickets( $database_id );
			};
			$fields['capacity']                 = function() use ( $database_id ) : ?int {
				return tribe_get_event_capacity( $database_id );
			};
			$fields['hasTickets']               = function() use ( $ticket_ids ) : bool {
				return ! empty( $ticket_ids );
			};
			$fields['hasTicketsOnSale']         = function() use ( $database_id ) : bool {
				return tribe_events_has_tickets_on_sale( $database_id );
			};
			$fields['hasUnlimitedStockTickets'] = function() use ( $database_id ) : bool {
				return tribe_events_has_unlimited_stock_tickets( $database_id );
			};
			$fields['isPartiallySoldOut']       = function() use ( $database_id ) : bool {
				return tribe_events_partially_soldout( $database_id );
			};
			$fields['isSoldOut']                = function() use ( $database_id ) : bool {
				return tribe_events_has_soldout( $database_id );
			};
			$fields['ticketDatabaseIds']        = function() use ( $ticket_ids ) : ?array {
				return $ticket_ids ?: null;
			};
			$fields['ticketIds']                = function() use ( $ticket_ids, $post_type ) : ?array {
				// @todo wrong post-type.
				return ! empty( $ticket_ids ) ? array_map( fn( $id ) => Relay::toGlobalId( $post_type, $id ), $ticket_ids ) : null;
			};

		return $fields;
	}
}
