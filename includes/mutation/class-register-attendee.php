<?php
/**
 * Mutation - registerAttendee
 *
 * Registers mutation for registering an attendee
 * purchasing a ticket.
 *
 * @package WPGraphQL\QL_Events\Mutation
 * @since 0.0.1
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
	 * @return array
	 */
	public static function get_input_fields() {
		$input_fields = array(
			'eventId'         => array(
				'type'        => array( 'non_null' => 'ID' ),
				'description' => __( 'Event ID', 'ql-events' ),
			),
			'ticketId'         => array(
				'type'        => array( 'non_null' => 'ID' ),
				'description' => __( 'Ticket ID', 'ql-events' ),
			),
			'name'             => array(
				'type'        => array( 'non_null' => 'String' ),
				'description' => __( 'Attendee Full Name', 'ql-events' ),
			),
			'email'            => array(
				'type'        => array( 'non_null' => 'String' ),
				'description' => __( 'Attendee Email', 'ql-events' ),
			),
			'orderId'          => array(
				'type'        => 'ID',
				'description' => __( 'Order ID', 'ql-events' ),
			),
			'orderAttendeeId'  => array(
				'type'        => 'String',
				'description' => __( 'Order Attendee ID', 'ql-events' ),
			),
			'userId'           => array(
				'type'        => 'ID',
				'description' => __( 'Attendee\'s User ID', 'ql-events' ),
			),
			'optout'           => array(
				'type'        => 'Boolean',
				'description' => __( 'Optout', 'ql-events' ),
			),
			'attendeeStatus'   => array(
				'type'        => 'String',
				'description' => __( 'Order status', 'ql-events' ),
			),
			'pricePaid'        => array(
				'type'        => 'Float',
				'description' => __( 'Price paid to attendee', 'ql-events' ),
			),
			'additionalFields' => array(
				'type'        => array( 'list_of' => 'MetaDataInput' ),
				'description' => __( 'Ticket additional fields input', 'ql-events' ),
			),
		);

		return $input_fields;
	}

	/**
	 * Defines the mutation output field configuration
	 *
	 * @return array
	 */
	public static function get_output_fields() {
		return [
			'attendee' => [
				'type'        => 'Attendee',
				'description' => __( 'Newly registered attendee', 'ql-events' ),
				'resolve'     => function( $payload, array $args, AppContext $context ) {
					if ( empty( $payload['id'] ) || ! absint( $payload['id'] ) ) {
						return null;
					}

					return get_post( $payload['id'] );
				},
			]
		];
	}

	/**
	 * Defines the mutation data modification closure.
	 *
	 * @return callable
	 */
	public static function mutate_and_get_payload() {
		return function( $input, AppContext $context, ResolveInfo $info ) {
			// Get input.
			$ticket_id         = Utils::get_database_id_from_id( $input['ticketId'] );
			$post_id           = Utils::get_database_id_from_id( $input['eventId'] );
			$full_name         = $input['name'];
			$email             = $input['email'];
			$order_id          = ! empty( $input['orderId'] )
				? Utils::get_database_id_from_id( $input['orderId'] )
				: md5( time() . rand() );
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
				: array();

			// Prep attendee data.
			$attendee_data = array_merge(
				array(
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
				),
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
			/** @var Tribe__Tickets__Attendees $attendees */
			$attendees = tribe( 'tickets.attendees' );
			$attendee = $attendees->create_attendee( $ticket, $attendee_data );;

			// Throw error if attendee not returned.
			if ( ! $attendee ) {
				throw new UserError( __( 'Failed to create attendee, check input and try again.', 'ql-events' ) );
			}
			// Return Attendee ID.
			return array( 'id' => $attendee->ID );
		};
	}

	private static function map_additional_fields( array $additional_fields ) {
		return array_reduce(
			$additional_fields,
			function( array $carry, array $item ) {
				$carry[ $item['key'] ] = $item['value'];

				return $carry;
			},
			array()
		);
	}
}
