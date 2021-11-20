<?php
/**
 * Factory Class
 *
 * This class serves as a factory for all ET resolvers.
 *
 * @package WPGraphQL\TEC\Tickets\Data
 * @since 0.0.1
 */

namespace WPGraphQL\TEC\Tickets\Data;

use GraphQLRelay\Relay;
use Tribe__Tickets__Tickets;
use Tribe__Repository;
use WP_Post;
use WP_Query;
use WPGraphQL\Model\Model as GraphQLModel;
use WPGraphQL\Model\Post;
use WPGraphQL\TEC\Tickets\Model;
use WPGraphQL\TEC\Tickets\Type\WPInterface;
use WPGraphQL\TEC\Common\Type\WPInterface as CommonInterface;
use WPGraphQL\TEC\Tickets\Type\WPObject;
use WPGraphQL\TEC\Traits\PostTypeResolverMethod;
use WPGraphQL\TEC\Utils\Utils;

/**
 * Class - Factory
 */
class Factory {
	use PostTypeResolverMethod;

	/**
	 * Overwrites the GraphQL config for auto-registered object types.
	 *
	 * @param array $config .
	 */
	public static function set_object_type_config( array $config ) : array {
		$post_type = Utils::graphql_type_to_post_type( $config['name'] );

		$post_types_with_tickets = Utils::get_enabled_post_types_for_tickets();

		if ( is_null( $post_type ) ) {
			return $config;
		}

		switch ( true ) {
			case 'tribe_rsvp_tickets' === $post_type:
				$config['interfaces'] = [ WPInterface\Ticket::$type ];
				break;
			case in_array( $post_type, [ 'tec_tc_ticket', 'tribe_tpp_tickets' ], true ):
				$config['interfaces'] = [ WPInterface\PurchasableTicket::$type ];
				break;
			case in_array( $post_type, $post_types_with_tickets, true ):
				$config['interfaces'] = array_merge(
					$config['interfaces'],
					[
						WPInterface\NodeWithTickets::$type,
						WPInterface\NodeWithAttendees::$type,
						CommonInterface\NodeWithJsonLd::$type,
					]
				);
				break;
			case in_array( $post_type, [ 'tec_tc_attendees', 'tribe_tpp_attendees', 'tribe_rsvp_attendees' ], true ):
				$config['interfaces'] = [ WPInterface\Attendee::$type ];
				break;
		}
		return $config;
	}

	/**
	 * Ensures the correct models are used even if the dataloader is wrong.
	 *
	 * @param null  $model  Possible model instance to be loader.
	 * @param mixed $entry  Data source.
	 * @return GraphQLModel|null
	 */
	public static function set_models_for_dataloaders( $model, $entry ) {
		if ( is_a( $entry, WP_Post::class ) ) {
			switch ( $entry->post_type ) {
				case 'tribe_rsvp_tickets':
					$model = new Model\RsvpTicket( $entry );
					break;
				case 'tec_tc_ticket':
				case 'tribe_tpp_tickets':
					$model = new Model\PurchasableTicket( $entry );
					break;
				case 'tec_tc_attendees':
				case 'tribe_rsvp_attendees':
				case 'tribe_tpp_attendees':
					$model = new Model\Attendee( $entry );
					break;
			}
		}

		return $model;
	}

	/**
	 * Resolves Relay node for some TEC GraphQL types.
	 *
	 * @param mixed $type     Node type.
	 * @param mixed $node     Node object.
	 *
	 * @return mixed
	 */
	public static function resolve_node_type( $type, $node ) {
		switch ( true ) {
			case is_a( $node, Model\RsvpTicket::class ):
				$type = Model\RsvpTicket::class;
				break;
			case is_a( $node, Model\PurchasableTicket::class ):
				$type = Model\PurchasableTicket::class;
				break;
			case is_a( $node, Model\Attendee::class ):
				$type = Model\Attendee::class;
		}

		return $type;
	}

	/**
	 * Overwrites the GraphQL config for auto-registered object types.
	 *
	 * @param array $config .
	 */
	public static function set_connection_type_config( array $config ) : array {
		// Return early if not RootQuery or EventsCategory.
		if ( 'RootQuery' !== $config['fromType'] ) {
			return $config;
		}

		switch ( $config['toType'] ) {
			case WPObject\RsvpTicket::$type:
			case 'TcTickets':
			case 'PayPalTickets':
				$config = TicketHelper::get_connection_config( $config );
				break;
			case 'RsvpAttendees':
			case 'TcAttendees':
			case 'PayPalAttendees':
				$config = AttendeeHelper::get_connection_config( $config );
				break;
		}

		return $config;
	}

	/**
	 * Extends event model with ticket fields.
	 *
	 * @param array $fields .
	 * @param Post  $model .
	 */
	public static function add_fields_to_event_model( array $fields, Post $model ) : array {
		$post_type   = $model->post_type;
		$database_id = (int) $model->ID;
		$provider    = Tribe__Tickets__Tickets::get_event_ticket_provider_object( $model->ID );

		$ticket_ids = ! empty( $provider ) ? $provider->get_tickets_ids( $database_id ) : null;

		$fields_to_add = [
			'availableTickets'         => function() use ( $database_id ) : int {
				return tribe_events_count_available_tickets( $database_id );
			},
			'capacity'                 => function() use ( $database_id ) : ?int {
				return tribe_get_event_capacity( $database_id );
			},
			'hasTickets'               => function() use ( $ticket_ids ) : bool {
				return ! empty( $ticket_ids );
			},
			'hasTicketsOnSale'         => function() use ( $database_id ) : bool {
				return tribe_events_has_tickets_on_sale( $database_id );
			},
			'hasUnlimitedStockTickets' => function() use ( $database_id ) : bool {
				return tribe_events_has_unlimited_stock_tickets( $database_id );
			},
			'isPartiallySoldOut'       => function() use ( $database_id ) : bool {
				return tribe_events_partially_soldout( $database_id );
			},
			'isSoldOut'                => function() use ( $database_id ) : bool {
				return tribe_events_has_soldout( $database_id );
			},
			'ticketDatabaseIds'        => function() use ( $ticket_ids ) : ?array {
				return $ticket_ids ?: null;
			},
			'ticketIds'                => function() use ( $ticket_ids, $post_type ) : ?array {
				return ! empty( $ticket_ids ) ? array_map( fn( $id ) => Relay::toGlobalId( $post_type, $id ), $ticket_ids ) : null;
			},
		];

		return array_merge( $fields, $fields_to_add );
	}

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

	/**
	 * Fixes the default orderby args set by TEC.
	 *
	 * @param array $query_args An array of the query arguments the query will be initialized with.
	 */
	public static function tribe_fix_orderby_args( array $query_args ) : array {
		// Checks if `orderby` isnt using an associative array.
		if ( isset( $query_args['orderby'] ) && is_array( $query_args['orderby'] ) && isset( $query_args['orderby'][0] ) ) {
			$orderby = [];
			$order   = $query_args['order'] ?? 'DESC';

			foreach ( $query_args['orderby'] as $field ) {
				$orderby[ $field ] = $order;
			}
			$query_args['orderby'] = $orderby;
		}

		return $query_args;
	}
}
