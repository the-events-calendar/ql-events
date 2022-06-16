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
		$input_fields = [
			'name'     => [
				'type'        => 'String',
				'description' => __( 'Attendee Full Name', 'ql-events' ),
			],
			'email'    => [
				'type'        => 'String',
				'description' => __( 'Attendee Email', 'ql-events' ),
			],
			'ticketId' => [
				'type'        => 'Int',
				'description' => __( 'Ticket ID', 'ql-events' ),
			],
		];

		if ( \QL_Events::is_ticket_events_plus_loaded() ) {
			$input_fields['customFields'] = [
				'type'        => [ 'list_of' => 'MetaDataInput' ],
				'description' => __( 'Ticket custom fields input', 'ql-events' ),
			];
		}

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
				'resolve'     => function( $payload ) {
					if ( empty( $payload['id'] ) || ! absint( $payload['id'] ) ) {
						return null;
					}

					return $context->get_loader( 'post' )->load_deferred( $payload['id'] );
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
		return function( $input, AppContext $context, ResolveInfo $info ) {
			$id = null;

			return [ 'id' => $id ];
		};
	}
}
