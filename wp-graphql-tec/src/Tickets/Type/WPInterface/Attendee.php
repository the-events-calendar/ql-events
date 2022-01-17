<?php
/**
 * GraphQL Object Type - Attendee
 *
 * @package WPGraphQL\TEC\Tickets\Type\WPInterface;
 * @since 0.0.1
 */

namespace WPGraphQL\TEC\Tickets\Type\WPInterface;

use GraphQL\Error\UserError;
use GraphQLRelay\Relay;
use WPGraphQL\AppContext;
use WPGraphQL\Registry\TypeRegistry;
use WPGraphQL\TEC\Events\Type\WPInterface\NodeWithEvent;
use WPGraphQL\TEC\Tickets\Data\AttendeeHelper;
use WPGraphQL\TEC\Tickets\Type\Enum\AttendeeTypeEnum;
use WPGraphQL\TEC\Tickets\Type\Enum\TicketIdTypeEnum;
use WPGraphQL\TEC\Utils\Utils;

/**
 * Class - Attendee
 */
class Attendee {
	/**
	 * Name of the type.
	 *
	 * @var string Type name.
	 */
	public static $type = 'Attendee';

	/**
	 * Registers GraphQL Interface
	 *
	 * @param TypeRegistry $type_registry .
	 */
	public static function register_interface( TypeRegistry &$type_registry ): void {
		register_graphql_interface_type(
			self::$type,
			[
				'description' => __( 'Attendee object', 'wp-graphql-tec' ),
				'interfaces'  => [
					'Node',
					'ContentNode',
					'UniformResourceIdentifiable',
					'DatabaseIdentifier',
					'NodeWithTitle',
					NodeWithOrder::$type,
					NodeWithTicket::$type,
					NodeWithUser::$type,
					NodeWithEvent::$type,
				],
				'fields'      => self::get_fields( $type_registry ),
				'resolveType' => function ( $value ) use ( &$type_registry ) {
					$possible_types = Utils::get_et_attendee_types();
					if ( isset( $possible_types[ $value->post_type ] ) ) {
						return $type_registry->get_type( $possible_types[ $value->post_type ] );
					}
					throw new UserError(
						sprintf(
							/* translators: %s: Attendee type */
							__( 'The "%s" attendee type is not supported by the core WPGraphQL for TEC schema.', 'wp-graphql-tec' ),
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
				'description' => __( 'An Attendee object', 'wp-graphql-tec' ),
				'args'        => [
					'id'     => [
						'type'        => [ 'non_null' => 'ID' ],
						'description' => __( 'The ID for identifying the Attendee', 'wp-graphql-tec' ),
					],
					'idType' => [
						'type'        => TicketIdTypeEnum::$type,
						'description' => __( 'Type of ID being used to identify the Attendee', 'wp-graphql-tec' ),
					],
				],
				'resolve'     => function ( $source, array $args, AppContext $context ) {
					$id      = $args['id'] ?? null;
					$id_type = $args['idType'] ?? 'global_id';

					$attendee_id = null;
					switch ( $id_type ) {
						case 'database_id':
							$attendee_id = absint( $id );
							break;
						case 'global_id':
							$id_components = Relay::fromGlobalId( $id );
							if ( empty( $id_components['id'] ) || empty( $id_components['type'] ) ) {
								throw new UserError( __( 'The global ID is invalid.', 'wp-graphql-tec' ) );
							}
							$attendee_id = absint( $id_components['id'] );
							break;
					}

					if ( empty( $attendee_id ) ) {
						/* translators: %1$s: ID type, %2$s: ID value */
						throw new UserError( sprintf( __( 'No attendee ID was found corresponding to the %1$s: %2$s', 'wp-graphql-tec' ), $id_type, $id ) );
					}

					$post_type         = get_post_type( $attendee_id );
					$ticket_post_types = array_keys( Utils::get_et_attendee_types() );

					if ( false === $post_type || ! in_array( $post_type, $ticket_post_types, true ) ) {
						/* translators: %1$s: ID type, %2$s: ID value */
						throw new UserError( sprintf( __( 'No attendee exists with the %1$s: %2$s', 'wp-graphql-tec' ), $id_type, $id ) );
					}

					return AttendeeHelper::resolve_object( $attendee_id, $context );
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
			'checkInStatus'    => [
				'type'        => 'String',
				'description' => __( 'Check-in status.', 'wp-graphql-tec' ),
			],
			'holderEmail'      => [
				'type'        => 'String',
				'description' => __( 'The email address of the ticket holder.', 'wp-graphql-tec' ),
			],
			'holderName'       => [
				'type'        => 'String',
				'description' => __( 'The name of the ticket holder.', 'wp-graphql-tec' ),
			],
			'isLegacyAttendee' => [
				'type'        => 'Boolean',
				'description' => __( 'Whether the attendee type uses a legacy CPT.', 'wp-graphql-tec' ),
			],
			'isPurchaser'      => [
				'type'        => 'Boolean',
				'description' => __( 'Whether the attendee is also the ticket purchaser.', 'wp-graphql-tec' ),
			],
			// @todo figure out what this field does.
			'isSubscribed'     => [
				'type'        => 'Boolean',
				'description' => __( 'Not sure what this means.', 'wp-graphql-tec' ),
			],
			'isOptout'         => [
				'type'        => 'Boolean',
				'description' => __( 'Whether to hide the attendee from the public list of attendees.', 'wp-graphql-tec' ),
			],
			'purchaseTime'     => [
				'type'        => 'String',
				'description' => __( 'The purchase time, in `Y-m-d H:i:s` format.', 'wp-graphql-tec' ),
			],
			'securityCode'     => [
				'type'        => 'String',
				'description' => __( 'The security code used to check in the attendee.', 'wp-graphql-tec' ),
			],
			'orderStatus'      => [
				'type'        => 'String',
				'description' => __( 'The current order status.', 'wp-graphql-tec' ),
			],
			'orderStatusLabel' => [
				'type'        => 'String',
				'description' => __( 'The label for the current order status.', 'wp-graphql-tec' ),
			],
			'purchaserName'    => [
				'type'        => 'String',
				'description' => __( 'The name of the ticket purchaser.', 'wp-graphql-tec' ),
			],
			'purchaserEmail'   => [
				'type'        => 'String',
				'description' => __( 'The email of the ticket purchaser.', 'wp-graphql-tec' ),
			],
			'type'             => [
				'type'        => AttendeeTypeEnum::$type,
				'description' => __( 'The attendee object type', 'wp-graphql-tec' ),
			],
		];
	}

}
