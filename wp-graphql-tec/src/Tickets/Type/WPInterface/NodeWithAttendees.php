<?php
/**
 * GraphQL Interface Type - NodeWithAttendees
 *
 * @package WPGraphQL\TEC\Tickets\Type\WPInterface;
 * @since 0.0.1
 */

namespace WPGraphQL\TEC\Tickets\Type\WPInterface;

use GraphQL\Type\Definition\ResolveInfo;
use GraphQLRelay\Relay;
use WPGraphQL\AppContext;
use WPGraphQL\Registry\TypeRegistry;
use WPGraphQL\TEC\Tickets\Data\AttendeeHelper;
use WPGraphQL\TEC\Tickets\Type\WPInterface\Attendee;

/**
 * Class - NodeWithAttendees
 */
class NodeWithAttendees {
	/**
	 * Name of the type.
	 *
	 * @var string Type name.
	 */
	public static $type = 'NodeWithAttendees';

	/**
	 * Registers GraphQL Interface
	 *
	 * @param TypeRegistry $type_registry .
	 */
	public static function register_interface( TypeRegistry &$type_registry ): void {
		register_graphql_interface_type(
			self::$type,
			[
				'description' => __( 'Attendee Fields', 'wp-graphql-tec' ),
				'connections' => [
					'attendees' => [
						'toType'  => Attendee::$type,
						'resolve' => function ( $source, array $args, AppContext $context, ResolveInfo $info ) {
							$attendee_ids = AttendeeHelper::get_attendee_ids( $source );

							if ( null === $attendee_ids ) {
								return null;
							}

							$args['where']['post__in'] = $attendee_ids;

							return AttendeeHelper::resolve_connection( $source, $args, $context, $info );
						},
					],
				],
				'fields'      => [
					'attendeeDatabaseIds' => [
						'type'        => [ 'list_of' => 'Int' ],
						'description' => __( 'The list of Attendee database IDs.', 'wp-graphql-tec' ),
						'resolve'     => fn( $source ) => AttendeeHelper::get_attendee_ids( $source ),
					],
					'attendeeIds'         => [
						'type'        => [ 'list_of' => 'ID' ],
						'description' => __( 'The list of Attendee global IDs.', 'wp-graphql-tec' ),
						'resolve'     => function( $source ) {
							$attendee_ids = AttendeeHelper::get_attendee_ids( $source );

							if ( null === $attendee_ids ) {
								return null;
							}

							// @todo wrong post type
							return array_map( fn( $id) => Relay::toGlobalId( $source->post_type, (string) $id ), $attendee_ids );
						},
					],
				],
			]
		);
	}

}
