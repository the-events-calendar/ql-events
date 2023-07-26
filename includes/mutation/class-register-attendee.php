<?php
/**
 * Mutation - registerAttendee
 *
 * Registers mutation for registering an attendee
 * that has purchased a ticket.
 *
 * @package WPGraphQL\QL_Events\Mutation
 * @since 0.2.0
 */

namespace WPGraphQL\QL_Events\Mutation;

use GraphQL\Error\UserError;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQLRelay\Relay;
use WPGraphQL\AppContext;
use WPGraphQL\Data\DataSource;
use WPGraphQL\Utils\Utils;
use WPGraphQL\Model\Post;

/**
 * Class Register_Attendee
 */
class Register_Attendee {

	/**
	 * Registers mutation
	 *
	 * @since 0.2.0
	 *
	 * @return void
	 */
	public static function register_mutation() {
		register_graphql_mutation(
			'registerAttendee',
			[
				'inputFields'         => self::get_input_fields(),
				'outputFields'        => self::get_output_fields(),
				'mutateAndGetPayload' => self::mutate_and_get_payload(),
			]
		);
	}

	/**
	 * Defines the mutation input field configuration
	 *
	 * @since 0.2.0
	 *
	 * @return array
	 */
	public static function get_input_fields() {
		$input_fields = [
			'eventId'          => [
				'type'        => [ 'non_null' => 'ID' ],
				'description' => __( 'Event ID', 'ql-events' ),
			],
			'ticketId'         => [
				'type'        => [ 'non_null' => 'ID' ],
				'description' => __( 'Ticket ID', 'ql-events' ),
			],
			'name'             => [
				'type'        => [ 'non_null' => 'String' ],
				'description' => __( 'Attendee Full Name', 'ql-events' ),
			],
			'email'            => [
				'type'        => [ 'non_null' => 'String' ],
				'description' => __( 'Attendee Email', 'ql-events' ),
			],
			'orderId'          => [
				'type'        => 'ID',
				'description' => __( 'Order ID', 'ql-events' ),
			],
			'orderAttendeeId'  => [
				'type'        => 'String',
				'description' => __( 'Order Attendee ID', 'ql-events' ),
			],
			'userId'           => [
				'type'        => 'ID',
				'description' => __( 'Attendee\'s User ID', 'ql-events' ),
			],
			'optout'           => [
				'type'        => 'Boolean',
				'description' => __( 'Optout', 'ql-events' ),
			],
			'attendeeStatus'   => [
				'type'        => 'String',
				'description' => __( 'Order status', 'ql-events' ),
			],
			'pricePaid'        => [
				'type'        => 'Float',
				'description' => __( 'Price paid to attendee', 'ql-events' ),
			],
			'additionalFields' => [
				'type'        => [ 'list_of' => 'MetaDataInput' ],
				'description' => __( 'Ticket additional fields input', 'ql-events' ),
			],
		];

		return $input_fields;
	}

	/**
	 * Defines the mutation output field configuration
	 *
	 * @since 0.2.0
	 *
	 * @return array
	 */
	public static function get_output_fields() {
		return [
			'attendee' => [
				'type'        => 'Attendee',
				'description' => __( 'Newly registered attendee', 'ql-events' ),
				'resolve'     => function( $payload, array $args, AppContext $context ) {
					if ( ! empty( $payload['id'] ) ) {
						$id = absint( $payload['id'] );
						return ! empty( $id ) ? $context->get_loader( 'post' )->load( $id ) : null;
					}

					return null;
				},
			],
		];
	}

	/**
	 * Defines the mutation data modification closure.
	 *
	 * @since 0.2.0
	 *
	 * @return callable
	 */
	public static function mutate_and_get_payload() {
		/**
		 * Registers attendee using provided input data.
		 *
		 * @param array       $input    Mutation input data.
		 * @param AppContext  $context  Mutation's AppContext instance.
		 * @param ResolveInfo $info     Mutation's ResolveInfo instance.
		 *
		 * @return array
		 */
		return function( $input, AppContext $context, ResolveInfo $info ) {
			// Get input.
			$ticket_id         = Utils::get_database_id_from_id( $input['ticketId'] );
			$post_id           = Utils::get_database_id_from_id( $input['eventId'] );
			$full_name         = $input['name'];
			$email             = $input['email'];
			$order_id          = ! empty( $input['orderId'] )
				? Utils::get_database_id_from_id( $input['orderId'] )
				: md5( time() . wp_rand() );
			$order_attendee_id = ! empty( $input['orderAttendeeId'] )
				? $input['orderAttendeeId']
				: null;
			$user_id           = ! empty( $input['userId'] )
				? Utils::get_database_id_from_id( $input['userId'] )
				: 0;
			$optout            = isset( $input['optout'] ) ? (int) $input['optout'] : 1;
			$attendee_status   = ! empty( $input['attendeeStatus'] ) ? $input['attendeeStatus'] : 'yes';
			$price_paid        = ! empty( $input['pricePaid'] ) ? $input['pricePaid'] : 0;

			$additional_fields = ! empty( $input['additionalFields'] )
				? self::map_additional_fields( $input['additionalFields'] )
				: [];

			// Prep attendee data.
			$attendee_data = array_merge(
				[
					'ticket_id'         => $ticket_id,
					'post_id'           => $post_id,
					'full_name'         => $full_name,
					'email'             => $email,
					'order_id'          => $order_id,
					'order_attendee_id' => $order_attendee_id,
					'user_id'           => $user_id,
					'optout'            => $optout,
					'attendee_status'   => $attendee_status,
					'price_paid'        => $price_paid,
				],
				$additional_fields,
			);

			// Get Ticket Provider.
			$provider = tribe_tickets_get_ticket_provider( $ticket_id );
			if ( ! $provider ) {
				throw new UserError( __( 'Provider for given ticket is not active.', 'ql-events' ) );
			}

			// Get ticket.
			$ticket = $provider->get_ticket( $post_id, $ticket_id );

			// Create attendee.
			$attendees = tribe( 'tickets.attendees' );
			$attendee  = $attendees->create_attendee( $ticket, $attendee_data );

			// Throw error if attendee not returned.
			if ( ! $attendee ) {
				throw new UserError( __( 'Failed to create attendee, check input and try again.', 'ql-events' ) );
			}
			// Return Attendee ID.
			return [ 'id' => $attendee->ID ];
		};
	}

	/**
	 * Simple reducer for mapping extra fields.
	 *
	 * @since 0.2.0
	 *
	 * @param array $additional_fields  Extra attendee meta.
	 *
	 * @return array
	 */
	protected static function map_additional_fields( array $additional_fields ) {
		return array_reduce(
			$additional_fields,
			function( array $carry, array $item ) {
				$carry[ $item['key'] ] = $item['value'];

				return $carry;
			},
			[]
		);
	}
}
