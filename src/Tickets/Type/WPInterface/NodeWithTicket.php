<?php
/**
 * GraphQL Interface Type - NodeWithTicket
 *
 * @package WPGraphQL\TEC\Tickets\Type\WPInterface;
 * @since 0.0.1
 */

namespace WPGraphQL\TEC\Tickets\Type\WPInterface;

use GraphQLRelay\Relay;
use WPGraphQL\AppContext;
use WPGraphQL\Registry\TypeRegistry;
use WPGraphQL\TEC\Tickets\Data\TicketHelper;
use WPGraphQL\TEC\Tickets\Data\VenueHelper;
use WPGraphQL\TEC\Tickets\Type\WPObject\Venue;

/**
 * Class - NodeWithTicket
 */
class NodeWithTicket {
	/**
	 * Name of the type.
	 *
	 * @var string Type name.
	 */
	public static $type = 'NodeWithTicket';

	/**
	 * Registers GraphQL Interface
	 *
	 * @param TypeRegistry $type_registry .
	 */
	public static function register_interface( TypeRegistry &$type_registry ): void {
		register_graphql_interface_type(
			self::$type,
			[
				'description' => __( 'Ticket fields', 'wp-graphql-tec' ),
				'fields'      => [
					'ticket'           => [
						'type'        => Ticket::$type,
						'description' => __( 'The ticket.', 'wp-graphql-tec' ),
						'resolve'     => function( $source, array $args, AppContext $context ) {
							if ( $source->ticketId === $source->ID ) {
								return null;
							}

							return TicketHelper::resolve_object( $source->ticketId, $context );
						},
					],
					'ticketDatabaseId' => [
						'type'        => 'Int',
						'description' => __( 'The ticket database ID.', 'wp-graphql-tec' ),
						'resolve'     => fn( $source ) => $source->ticketId !== $source->ID ? $source->ticketId : null,
					],
					'ticketId'         => [
						'type'        => 'ID',
						'description' => __( 'The ticket global ID.', 'wp-graphql-tec' ),
						'resolve'     => function( $source ) {
							if ( $source->ticketId === $source->ID ) {
								return null;
							}
							// @todo make generic.
							return Relay::toGlobalId( 'tribe_ticket', (string) $source->ticketId );
						},
					],
					'ticketName'       => [
						'type'        => 'String',
						'description' => __( 'The ticket name', 'wp-graphql-tec' ),
					],
				],
			]
		);
	}
}
