<?php
/**
 * GraphQL Object Type - OrderItem
 *
 * @package WPGraphQL\TEC\Tickets\Type\WPObject
 * @since 0.0.1
 */

namespace WPGraphQL\TEC\Tickets\Type\WPObject;

use WPGraphQL\AppContext;
use WPGraphQL\TEC\Tickets\Data\TicketHelper;
use WPGraphQL\TEC\Tickets\Type\WPInterface\Ticket;

/**
 * Class - OrderItem
 */
class OrderItem {

	/**
	 * Name of the type.
	 *
	 * @var string Type name.
	 */
	public static $type = 'OrderItem';
	/**
	 * {@inheritDoc}
	 */
	public static function register_type() : void {
		register_graphql_object_type(
			self::$type,
			[
				'description' => __( 'The order Item.', 'wp-graphql-tec' ),
				'fields'      => [
					'databaseId' => [
						'type'        => 'Int',
						'description' => __( 'The ticket database ID.', 'wp-graphql-tec' ),
						'resolve'     => fn( $source ) : ?int => $source['ticket_id'] ?? null,
					],
					'iac'        => [
						'type'        => 'String',
						'description' => __( 'The IAC setting for the item.', 'wp-graphql-tec' ),
						'resolve'     => fn( $source ) : ?string => isset( $source['extra']['iac'] ) ? $source['extra']['iac'] : null,
					],
					'isOptout'   => [
						'type'        => 'Boolean',
						'description' => __( 'Whether to hide the attendee from the public list of attendees.', 'wp-graphql-tec' ),
						'resolve'     => fn( $source ) : bool => (bool) $source['extra']['optout'],
					],
					'price'      => [
						'type'        => 'Float',
						'description' => __( 'The item price.', 'wp-graphql-tec' ),
						'resolve'     => fn( $source ) : ?float => $source['price'] ?? null,
					],
					'quantity'   => [
						'type'        => 'Int',
						'description' => __( 'The quantity ordered.', 'wp-graphql-tec' ),
						'resolve'     => fn( $source ) : ?int => $source['quantity'] ?? null,
					],
					'subtotal'   => [
						'type'        => 'Float',
						'description' => __( 'The order item subtotal.', 'wp-graphql-tec' ),
						'resolve'     => fn( $source ) : ?float => $source['sub_total'] ?? null,
					],
					'ticket'     => [
						'type'        => Ticket::$type,
						'description' => __( 'The ticket.', 'wp-graphql-tec' ),
						'resolve'     => function( $source, array $args, AppContext $context ) {
							return TicketHelper::resolve_object( $source['ticket_id'], $context );
						},
					],
				],
			],
		);
	}
}
