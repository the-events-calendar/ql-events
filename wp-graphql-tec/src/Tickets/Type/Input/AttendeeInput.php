<?php
/**
 * GraphQL Input Type - AttendeeInput
 *
 * @package WPGraphQL\TEC\Tickets\Type\Input
 * @since 0.0.1
 */

namespace WPGraphQL\TEC\Tickets\Type\Input;

use WPGraphQL\TEC\Tickets\Type\Enum\OrderStatusEnum;

/**
 * Class - AttendeeInput
 */
class AttendeeInput {

	/**
	 * Name of the type.
	 *
	 * @var string Type name.
	 */
	public static $type = 'AttendeeInput';
	/**
	 * {@inheritDoc}
	 */
	public static function register_type() : void {
		register_graphql_input_type(
			self::$type,
			[
				'description' => __( 'The attendee information for the mutation.', 'wp-graphql-tec' ),
				'fields'      => [
					'orderStatus'        => [
						'type'        => [ 'non_null' => 'Rsvp' . OrderStatusEnum::$type ],
						'description' => 'The RSVP order status',
					],
					'shouldHidePublicly' => [
						'type'        => [ 'non_null' => 'Boolean' ],
						'description' => __( 'Whether the attendees should be hidden from public lists', 'wp-graphql-tec' ),
					],
					'name'               => [
						'type'        => [ 'non_null' => 'String' ],
						'description' => __( 'The name of the attendee', 'wp-graphql-tec' ),
					],
					'email'              => [
						'type'        => [ 'non_null' => 'String' ],
						'description' => __( 'The emaill address of the attendee attendee', 'wp-graphql-tec' ),
					],
				],
			]
		);
	}
}
