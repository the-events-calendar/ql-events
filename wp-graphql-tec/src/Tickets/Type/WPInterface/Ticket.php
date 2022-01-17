<?php
/**
 * GraphQL Object Type - Ticket
 *
 * @package WPGraphQL\TEC\Tickets\Type\WPInterface;
 * @since 0.0.1
 */

namespace WPGraphQL\TEC\Tickets\Type\WPInterface;

use GraphQL\Error\UserError;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQLRelay\Relay;
use WPGraphQL\AppContext;
use WPGraphQL\Registry\TypeRegistry;
use WPGraphQL\TEC\Events\Type\WPInterface\NodeWithEvent;
use WPGraphQL\TEC\Tickets\Data\AttendeeHelper;
use WPGraphQL\TEC\Tickets\Data\TicketHelper;
use WPGraphQL\TEC\Tickets\Type\Enum\StockModeEnum;
use WPGraphQL\TEC\Tickets\Type\Enum\TicketIdTypeEnum;
use WPGraphQL\TEC\Tickets\Type\Enum\TicketTypeEnum;
use WPGraphQL\TEC\Utils\Utils;

/**
 * Class - Ticket
 */
class Ticket {
	/**
	 * Name of the type.
	 *
	 * @var string Type name.
	 */
	public static $type = 'Ticket';

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
				'interfaces'  => [
					'Node',
					'ContentNode',
					'UniformResourceIdentifiable',
					'DatabaseIdentifier',
					'NodeWithTitle',
					'NodeWithFeaturedImage',
					NodeWithEvent::$type,
					NodeWithAttendees::$type,
				],
				'fields'      => self::get_fields( $type_registry ),
				'resolveType' => function ( $value ) use ( &$type_registry ) {
					$possible_types = Utils::get_et_ticket_types();
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
							if ( empty( $id_components['id'] ) || empty( $id_components['type'] ) ) {
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
					$ticket_post_types = array_keys( Utils::get_et_ticket_types() );

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
			'capacity'        => [
				'type'        => 'Int',
				'description' => __( 'Ticket capacity.', 'wp-graphql-tec' ),
			],
			'description'     => [
				'type'        => 'String',
				'description' => __( 'Free text with a description of the ticket.', 'wp-graphql-tec' ),
			],
			'endDate'         => [
				'type'        => 'String',
				'description' => __( 'Date the ticket should stop being sold.', 'wp-graphql-tec' ),
			],
			'endTime'         => [
				'type'        => 'String',
				'description' => __( 'Time the ticket should stop being sold.', 'wp-graphql-tec' ),
			],
			'iac'             => [
				'type'        => 'String',
				'description' => __( 'The IAC setting for the ticket.', 'wp-graphql-tec' ),
			],
			'isManagingStock' => [
				'type'        => 'Boolean',
				'description' => __( 'Whether the stock is being managed.', 'wp-graphql-tec' ),
			],
			'price'           => [
				'type'        => 'Float',
				'description' => __( 'Current sale price, without any sign.', 'wp-graphql-tec' ),
			],
			'quantitySold'    => [
				'type'        => 'Int',
				'description' => __( 'Number of tickets of this kind sold.', 'wp-graphql-tec' ),
			],
			'purchaseLimit'   => [
				'type'        => 'String',
				'description' => __( 'Purchase limit for the ticket.', 'wp-graphql-tec' ),
			],
			'showDescription' => [
				'type'        => 'Boolean',
				'description' => __( 'Whether to show the description on the front end and in emails.', 'wp-graphql-tec' ),
			],
			'startDate'       => [
				'type'        => 'String',
				'description' => __( 'Date the ticket should be put on sale.', 'wp-graphql-tec' ),
			],
			'startTime'       => [
				'type'        => 'String',
				'description' => __( 'Time the ticket should be put on sale.', 'wp-graphql-tec' ),
			],
			'stock'           => [
				'type'        => 'String',
				'description' => __( 'Amount of tickets of this kind in stock.', 'wp-graphql-tec' ),
			],
			'stockMode'       => [
				'type'        => StockModeEnum::$type,
				'description' => __( 'The mode of stock handling to be used for the ticket when global stock is enabled for the event.', 'wp-graphql-tec' ),
			],
			'type'            => [
				'type'        => TicketTypeEnum::$type,
				'description' => __( 'The ticket object type', 'wp-graphql-tec' ),
			],
		];
	}

}
