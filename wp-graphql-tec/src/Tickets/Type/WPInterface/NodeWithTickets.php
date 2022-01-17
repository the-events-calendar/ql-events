<?php
/**
 * GraphQL Object Type - NodeWithTickets
 *
 * @package WPGraphQL\TEC\Tickets\Type\WPInterface;
 * @since 0.0.1
 */

namespace WPGraphQL\TEC\Tickets\Type\WPInterface;

use GraphQL\Type\Definition\ResolveInfo;
use GraphQLRelay\Relay;
use WPGraphQL\AppContext;
use WPGraphQL\Registry\TypeRegistry;
use WPGraphQL\TEC\Tickets\Data\TicketHelper;
use WPGraphQL\TEC\Tickets\Type\WPInterface\Ticket;

/**
 * Class - NodeWithTickets
 */
class NodeWithTickets {
	/**
	 * Name of the type.
	 *
	 * @var string Type name.
	 */
	public static $type = 'NodeWithTickets';

	/**
	 * Registers GraphQL Interface
	 *
	 * @param TypeRegistry $type_registry .
	 */
	public static function register_interface( TypeRegistry &$type_registry ): void {
		register_graphql_interface_type(
			self::$type,
			[
				'description' => __( 'Ticket object', 'wp-graphql-tec' ),
				'connections' => [
					'tickets' => [
						'toType'  => Ticket::$type,
						'resolve' => function ( $source, array $args, AppContext $context, ResolveInfo $info ) {
							$ticket_ids = TicketHelper::get_ticket_ids( $source );

							if ( null === $ticket_ids ) {
								return null;
							}

							$args['where']['post__in'] = $ticket_ids;

							return TicketHelper::resolve_connection( $source, $args, $context, $info, Ticket::$type );
						},
					],
				],
				'fields'      => [
					'availableTickets'         => [
						'type'        => 'Int',
						'description' => __( 'The total number of tickets still available for sale for a specific event.', 'wp-graphql-tec' ),
					],
					'capacity'                 => [
						'type'        => 'Int',
						'description' => __( 'The capacity for the given event.', 'wp-graphql-tec' ),
					],
					'hasTickets'               => [
						'type'        => 'Boolean',
						'description' => __( 'Whether any tickets exist for the current event.', 'wp-graphql-tec' ),
					],
					'hasTicketsOnSale'         => [
						'type'        => 'Boolean',
						'description' => __( 'Whether the event has any tickets on sale.', 'wp-graphql-tec' ),
					],
					'hasUnlimitedStockTickets' => [
						'type'        => 'Boolean',
						'description' => __( 'Whether the event contains one or more tickets which are not subject to any inventory limitations.', 'wp-graphql-tec' ),
					],
					'isPartiallySoldOut'       => [
						'type'        => 'Boolean',
						'description' => __( 'Whether one or more of the tickets available for this event (but not all) have sold out', 'wp-graphql-tec' ),
					],
					'isSoldOut'                => [
						'type'        => 'Boolean',
						'description' => __( 'Whether the event has sold out of tickets.', 'wp-graphql-tec' ),
					],
					'ticketIds'                => [
						'type'        => [ 'list_of' => 'ID' ],
						'description' => __( 'An array of globally unique ID of the tickets assigned to the node', 'wp-graphql-tec' ),
						'resolve'     => function ( $source ) {
							$ids = TicketHelper::get_ticket_ids( $source );

							if ( null === $ids ) {
								return null;
							}

							// @todo this is wrong. Needs to be the ticket type.
							return array_map( fn( $id ) => Relay::toGlobalId( $source->post_type, $id ), $ids );
						},
					],
					'ticketDatabaseIds'        => [
						'type'        => [ 'list_of' => 'ID' ],
						'description' => __( 'An array of globally unique ID of the tickets assigned to the node', 'wp-graphql-tec' ),
						'resolve'     => function ( $source ) {
							return TicketHelper::get_ticket_ids( $source );
						},
					],
				],
			]
		);
	}


}
