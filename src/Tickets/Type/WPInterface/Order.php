<?php
/**
 * GraphQL Object Type - Order
 *
 * @package WPGraphQL\TEC\Tickets\Type\WPInterface;
 * @since 0.0.1
 */

namespace WPGraphQL\TEC\Tickets\Type\WPInterface;

use GraphQL\Error\UserError;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQLRelay\Relay;
use WPGraphQL\AppContext;
use WPGraphQL\Data\DataSource;
use WPGraphQL\Registry\TypeRegistry;
use WPGraphQL\TEC\Events\Data\EventHelper;
use WPGraphQL\TEC\Events\Type\WPObject\Event;
use WPGraphQL\TEC\Tickets\Type\WPObject\OrderItem;
use WPGraphQL\TEC\Tickets\Data\OrderHelper;
use WPGraphQL\TEC\Tickets\Data\TicketHelper;
use WPGraphQL\TEC\Tickets\Type\Enum\CurrencyCodeEnum;
use WPGraphQL\TEC\Tickets\Type\Enum\OrderTypeEnum;
use WPGraphQL\TEC\Tickets\Type\Enum\PaymentGatewaysEnum;
use WPGraphQL\TEC\Tickets\Type\Enum\TicketIdTypeEnum;
use WPGraphQL\TEC\Utils\Utils;

/**
 * Class - Order
 */
class Order {
	/**
	 * Name of the type.
	 *
	 * @var string Type name.
	 */
	public static $type = 'Order';

	/**
	 * Registers GraphQL Interface
	 *
	 * @param TypeRegistry $type_registry .
	 */
	public static function register_interface( TypeRegistry &$type_registry ): void {
		register_graphql_interface_type(
			self::$type,
			[
				'description' => __( 'Order object', 'wp-graphql-tec' ),
				'interfaces'  => [
					'Node',
					'ContentNode',
					'UniformResourceIdentifiable',
					'DatabaseIdentifier',
				],
				'connections' => [
					'tickets' => [
						'toType'  => Ticket::$type,
						'resolve' => function( $source, array $args, AppContext $context, ResolveInfo $info ) {
							$ticket_ids = TicketHelper::get_ticket_ids( $source );

							if ( null === $ticket_ids ) {
								return null;
							}

							$args['where']['post__in'] = $ticket_ids;

							return TicketHelper::resolve_connection( $source, $args, $context, $info );
						},
					],
					'events'  => [
						'toType'  => Event::$type,
						'resolve' => function( $source, array $args, AppContext $context, ResolveInfo $info ) {
							if ( empty( $source->eventDatabaseIds ) ) {
								return null;
							}

							$args['where']['post__in'] = $source->eventDatabaseIds;

							return EventHelper::resolve_connection( $source, $args, $context, $info );
						},
					],
				],
				'fields'      => self::get_fields( $type_registry ),
				'resolveType' => function ( $value ) use ( &$type_registry ) {
					$possible_types = Utils::get_et_order_types();
					if ( isset( $possible_types[ $value->post_type ] ) ) {
						return $type_registry->get_type( $possible_types[ $value->post_type ] );
					}
					throw new UserError(
						sprintf(
							/* translators: %s: Order type */
							__( 'The "%s" order type is not supported by the core WPGraphQL for TEC schema.', 'wp-graphql-tec' ),
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
				'description' => __( 'An Order object', 'wp-graphql-tec' ),
				'args'        => [
					'id'     => [
						'type'        => [ 'non_null' => 'ID' ],
						'description' => __( 'The ID for identifying the Order', 'wp-graphql-tec' ),
					],
					'idType' => [
						'type'        => TicketIdTypeEnum::$type,
						'description' => __( 'Type of ID being used to identify the Order', 'wp-graphql-tec' ),
					],
				],
				'resolve'     => function ( $source, array $args, AppContext $context ) {
					$id      = $args['id'] ?? null;
					$id_type = $args['idType'] ?? 'global_id';

					$order_id = null;
					switch ( $id_type ) {
						case 'database_id':
							$order_id = absint( $id );
							break;
						case 'global_id':
							$id_components = Relay::fromGlobalId( $id );
							if ( empty( $id_components['id'] ) || empty( $id_componenets['type'] ) ) {
								throw new UserError( __( 'The global ID is invalid.', 'wp-graphql-tec' ) );
							}
							$order_id = absint( $id_components['id'] );
							break;
					}

					if ( empty( $order_id ) ) {
						/* translators: %1$s: ID type, %2$s: ID value */
						throw new UserError( sprintf( __( 'No Order ID was found corresponding to the %1$s: %2$s', 'wp-graphql-tec' ), $id_type, $id ) );
					}

					$post_type         = get_post_type( $order_id );
					$ticket_post_types = array_keys( Utils::get_et_order_types() );

					if ( false === $post_type || ! in_array( $post_type, $ticket_post_types, true ) ) {
						/* translators: %1$s: ID type, %2$s: ID value */
						throw new UserError( sprintf( __( 'No Order exists with the %1$s: %2$s', 'wp-graphql-tec' ), $id_type, $id ) );
					}

					return OrderHelper::resolve_object( $order_id, $context );
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
			'currency'          => [
				'type'        => CurrencyCodeEnum::$type,
				'description' => __( 'The currrency code.', 'wp-graphql-tec' ),
			],
			'eventDatabaseIds'  => [
				'type'        => [ 'list_of' => 'Int' ],
				'description' => __( 'The ids of the events associated with this order.', 'wp-graphql-tec' ),
			],
			'formattedTotal'    => [
				'type'        => 'String',
				'description' => __( 'The formatted total cost of the order.', 'wp-graphql-tec' ),
			],
			'gateway'           => [
				'type'        => PaymentGatewaysEnum::$type,
				'description' => __( 'The gateway used for the order.', 'wp-graphql-tec' ),
			],
			'gatewayOrderId'    => [
				'type'        => 'ID',
				'description' => __( 'The order ID generated by the gateway', 'wp-graphql-tec' ),
			],
			'hash'              => [
				'type'        => 'String',
				'description' => __( 'The unique hash for the order.', 'wp-graphql-tec' ),
			],
			'items'             => [
				'type'        => [ 'list_of' => OrderItem::$type ],
				'description' => __( 'The list of items (tickets) in the order.', 'wp-graphql-tec' ),
			],
			'status'            => [
				'type'        => 'String',
				'description' => __( 'The current order status.', 'wp-graphql-tec' ),
			],
			'purchaser'         => [
				'type'        => 'User',
				'description' => __( 'The user who purchased the ticket', 'wp-graphql-tec' ),
				'resolve'     => function( $source, array $args, AppContext $context ) {
					return isset( $source->purchaser['user_id'] ) ? DataSource::resolve_user( $source->purchaser['user_id'], $context ) : null;
				},
			],
			'purchaserName'     => [
				'type'        => 'String',
				'description' => __( 'The name of the ticket purchaser.', 'wp-graphql-tec' ),
			],
			'purchaserEmail'    => [
				'type'        => 'String',
				'description' => __( 'The email of the ticket purchaser.', 'wp-graphql-tec' ),
			],
			'purchaseTime'      => [
				'type'        => 'String',
				'description' => __( 'The purchase time, in `Y-m-d H:i:s` format.', 'wp-graphql-tec' ),
			],
			'ticketDatabaseIds' => [
				'type'        => [ 'list_of' => 'Int' ],
				'description' => __( 'The ids of the tickets associated with this order.', 'wp-graphql-tec' ),
			],
			'totalValue'        => [
				'type'        => 'Float',
				'description' => __( 'The ids of the tickets associated with this order.', 'wp-graphql-tec' ),
			],
			'type'              => [
				'type'        => OrderTypeEnum::$type,
				'description' => __( 'The Order object type', 'wp-graphql-tec' ),
			],
		];
	}
}
