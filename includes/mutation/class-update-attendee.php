<?php
/**
 * Mutation - updateAttendee
 *
 * Registers mutation for update an existing attendee
 * who has purchased a ticket.
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
 * Class Update_Attendee
 */
class Update_Attendee extends Register_Attendee {

	/**
	 * Registers mutation
	 *
	 * @since 0.2.0
	 *
	 * @return void
	 */
	public static function register_mutation() {
		register_graphql_mutation(
			'updateAttendee',
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
			'attendeeId'       => [
				'type'        => [ 'non_null' => 'ID' ],
				'description' => __( 'Event ID', 'ql-events' ),
			],
			'name'             => [
				'type'        => 'String',
				'description' => __( 'Attendee Full Name', 'ql-events' ),
			],
			'email'            => [
				'type'        => 'String',
				'description' => __( 'Attendee Email', 'ql-events' ),
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
				'description' => __( 'Updated attendee', 'ql-events' ),
				'resolve'     => function( $payload, array $args, AppContext $context ) {
					if ( empty( $payload['id'] ) || ! absint( $payload['id'] ) ) {
						return null;
					}

					return new Post( get_post( $payload['id'] ) );
				},
			],
		];
	}

	/**
	 * Defines the mutation data modification closure.
	 *
	 * @return callable
	 */
	public static function mutate_and_get_payload() {
		/**
		 * Updates existing attendee using provided input data.
		 *
		 * @since 0.2.0
		 *
		 * @param array       $input    Mutation input data.
		 * @param AppContext  $context  Mutation's AppContext instance.
		 * @param ResolveInfo $info     Mutation's ResolveInfo instance.
		 *
		 * @return array
		 */
		return function( $input, AppContext $context, ResolveInfo $info ) {
			// Get Attendee ID.
			$attendee_id = Utils::get_database_id_from_id( $input['attendeeId'] );
			$provider    = tribe_tickets_get_ticket_provider( $attendee_id );
			if ( ! $provider ) {
				throw new UserError( __( 'No ticket provider found for this\'s attendee\'s ticket', 'ql-events' ) );
			}

			$attendee_data = (array) $provider->get_attendee( $attendee_id );

			// Check input and prep attendee data.
			if ( ! empty( $input['name'] ) ) {
				$attendee_data['full_name'] = $input['name'];
			}
			if ( ! empty( $input['email'] ) ) {
				$attendee_data['email'] = $input['email'];
			}
			if ( ! empty( $input['userId'] ) ) {
				$attendee_data['user_id'] = Utils::get_database_id_from_id( $input['userId'] );
			}
			if ( ! empty( $input['optout'] ) ) {
				$attendee_data['optout'] = $input['optout'];
			}
			if ( ! empty( $input['attendeeStatus'] ) ) {
				$attendee_data['attendee_status'] = $input['attendeeStatus'];
			}
			if ( ! empty( $input['additionalFields'] ) ) {
				$additional_fields = self::map_additional_fields( $input['additionalFields'] );
				$overwrite         = ! isset( $additional_fields['overwrite'] ) && 'yes' === $additional_fields['overwrite'];
				if ( $overwrite ) {
					unset( $additional_fields['overwrite'] );
					$attendee_data['attendee_meta'] = $additional_fields;
				} else {
					$attendee_data['attendee_meta'] = ! empty( $attendee_data['attendee_meta'] )
					? array_merge( $attendee_data['attendee_meta'], $additional_fields )
					: $additional_fields;
				}
			}

			// Update attendee.
			$attendees = tribe( 'tickets.attendees' );
			$success   = $attendees->update_attendee( $attendee_id, $attendee_data );

			// Throw error if attendee not updated.
			if ( ! $success ) {
				throw new UserError( __( 'Failed to update attendee, check input and try again.', 'ql-events' ) );
			}
			// Return Attendee ID.
			return [ 'id' => $attendee_id ];
		};
	}
}
