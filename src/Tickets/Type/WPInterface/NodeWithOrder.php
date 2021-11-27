<?php
/**
 * GraphQL Interface Type - NodeWithOrder
 *
 * @package WPGraphQL\TEC\Tickets\Type\WPInterface;
 * @since 0.0.1
 */

namespace WPGraphQL\TEC\Tickets\Type\WPInterface;

use WPGraphQL\AppContext;
use WPGraphQL\Registry\TypeRegistry;
use WPGraphQL\TEC\Tickets\Data\OrderHelper;
use WPGraphQL\TEC\Tickets\Type\WPInterface\Order;

/**
 * Class - NodeWithOrder
 */
class NodeWithOrder {
	/**
	 * Name of the type.
	 *
	 * @var string Type name.
	 */
	public static $type = 'NodeWithOrder';

	/**
	 * Registers GraphQL Interface
	 *
	 * @param TypeRegistry $type_registry .
	 */
	public static function register_interface( TypeRegistry &$type_registry ): void {
		register_graphql_interface_type(
			self::$type,
			[
				'description' => __( 'Order Fields', 'wp-graphql-tec' ),
				'connections' => [
					'order' => [
						'toType'  => Order::$type,
						'resolve' => function ( $source, array $args, AppContext $context ) {
							if ( empty( $source->orderId ) ) {
								return null;
							}
						},
					],
				],
				'fields'      => [
					'order'           => [
						'type'        => Order::$type,
						'description' => __( 'The order', 'wp-graphql-tec' ),
						'resolve'     => function( $source, array $args, AppContext $context ) {
							if ( $source->orderId === $source->ID ) {
								return null;
							}
							return OrderHelper::resolve_object( $source->orderId, $context );
						},
					],
					'orderDatabaseId' => [
						'type'        => [ 'list_of' => 'Int' ],
						'description' => __( 'The list of Order database IDs.', 'wp-graphql-tec' ),
						'resolve'     => fn( $source ) : ?int => $source->orderId ?? null,
					],
				],
			]
		);
	}

}
