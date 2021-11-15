<?php
/**
 * GraphQL Object Type - PurchasableTicket
 *
 * @package WPGraphQL\TEC\Tickets\Type\WPInterface;
 * @since 0.0.1
 */

namespace WPGraphQL\TEC\Tickets\Type\WPInterface;

use GraphQL\Error\UserError;
use GraphQLRelay\Relay;
use WPGraphQL\AppContext;
use WPGraphQL\Registry\TypeRegistry;
use WPGraphQL\TEC\Tickets\Data\Factory;
use WPGraphQL\TEC\Tickets\Data\TicketHelper;
use WPGraphQL\TEC\Tickets\Type\Enum\TicketIdTypeEnum;
use WPGraphQL\TEC\Utils\Utils;

/**
 * Class - PurchasableTicket
 */
class PurchasableTicket {
	/**
	 * Name of the type.
	 *
	 * @var string Type name.
	 */
	public static $type = 'PurchasableTicket';

	/**
	 * Registers GraphQL Interface
	 *
	 * @param TypeRegistry $type_registry .
	 */
	public static function register_interface( TypeRegistry &$type_registry ): void {
		register_graphql_interface_type(
			self::$type,
			[
				'description' => __( 'Purchasable Ticket object', 'wp-graphql-tec' ),
				'interfaces'  => [ 'Node', 'ContentNode', 'UniformResourceIdentifiable', 'DatabaseIdentifier', 'NodeWithTitle', 'NodeWithFeaturedImage', 'Ticket' ],
				'fields'      => self::get_fields( $type_registry ),
				'resolveType' => function ( $value ) use ( &$type_registry ) {
					$possible_types = Utils::get_et_types();
					if ( isset( $possible_types[ $value->post_type ] ) ) {
						return $type_registry->get_type( $possible_types[ $value->post_type ] );
					}

					throw new UserError(
						sprintf(
							/* translators: %s: Product type */
							__( 'The "%s" product type is not supported by the core WPGraphQL for TEC schema.', 'wp-graphql-tec' ),
							$value->type
						)
					);
				},
			]
		);

		register_graphql_field(
			'RootQuery',
			self::$type,
			[
				'type'        => self::$type,
				'description' => __( 'A ticket object', 'wp-graphql-tec' ),
				'args'        => [
					'id'     => [
						'type'        => [ 'non_null' => 'ID' ],
						'description' => __( 'The ID for identifying the ticket', 'wp-graphql-tec' ),
					],
					'idType' => [
						'type'        => TicketIdTypeEnum::$type,
						'description' => __( 'Type of ID being used to identify the ticket', 'wp-graphql-tec' ),
					],
				],
				'resolve'     => function ( $source, array $args, AppContext $context ) {
					$id      = $args['id'] ?? null;
					$id_type = $args['idType'] ?? 'global_id';

					$ticket_id = null;
					switch ( $id_type ) {
						case 'database_id':
							$ticket_id = absint( $id );
							break;
						case 'global_id':
							$id_components = Relay::fromGlobalId( $id );
							if ( empty( $id_components['id'] ) || empty( $id_componenets['type'] ) ) {
								throw new UserError( __( 'The global ID is invalid.', 'wp-graphql-tec' ) );
							}
							$ticket_id = absint( $id_components['id'] );
							break;
					}

					if ( empty( $ticket_id ) ) {
						/* translators: %1$s: ID type, %2$s: ID value */
						throw new UserError( sprintf( __( 'No ticket ID was found corresponding to the %1$s: %2$s', 'wp-graphql-tec' ), $id_type, $id ) );
					}

					$post_type         = get_post_type( $ticket_id );
					$ticket_post_types = array_keys( Utils::get_et_types() );

					if ( false === $post_type || ! in_array( $post_type, $ticket_post_types, true ) ) {
						/* translators: %1$s: ID type, %2$s: ID value */
						throw new UserError( sprintf( __( 'No ticket exists with the %1$s: %2$s', 'wp-graphql-tec' ), $id_type, $id ) );
					}

					return TicketHelper::resolve_object( $ticket_id, $context );
				},

			]
		);
	}

	/**
	 * Gets the fields for the interface.
	 *
	 * @param TypeRegistry $type_registry .
	 */
	public static function get_fields( TypeRegistry $type_registry ) : array {
		return [
			'isOnSale'          => [
				'type'        => 'Boolean',
				'description' => __( 'Indicates if the ticket is currently being offered at a reduced price as part of a special sale.', 'wp-graphql-tec' ),
			],
			'priceSuffix'       => [
				'type'        => 'String',
				'description' => __( 'The price suffix.', 'wp-graphql-tec' ),
			],
			'quantityCancelled' => [
				'type'        => 'Int',
				'description' => __( 'Number of tickets for which an order has been cancelled', 'wp-graphql-tec' ),
			],
			'quantityPending'   => [
				'type'        => 'Int',
				'description' => __( 'Number of tickets for which an order has been placed but not confirmed or \"completed\".', 'wp-graphql-tec' ),
			],
			'quantityRefunded'  => [
				'type'        => 'Int',
				'description' => __( 'Number of tickets for which an order has been refunded', 'wp-graphql-tec' ),
			],
			'regularPrice'      => [
				'type'        => 'Int',
				'description' => __( 'Regular price (if the ticket is not on a special sale this will be identical to $price).', 'wp-graphql-tec' ),

				'sku'         => [
					'type'        => 'String',
					'description' => __( 'Tthe SKU for the ticket.', 'wp-graphql-tec' ),
				],
				'stockCap'    => [
					'type'        => 'Int',
					'description' => __( 'The maximum permitted number of sales for this ticket when global stock is enabled for the event and CAPPED_STOCK_MODE is in effect.', 'wp-graphql-tec' ),
				],
			],
		];
	}
}
