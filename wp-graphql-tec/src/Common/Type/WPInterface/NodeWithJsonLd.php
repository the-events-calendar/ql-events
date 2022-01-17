<?php
/**
 * GraphQL Object Type - NodeWithJsonLd
 *
 * @package WPGraphQL\TEC\Common\Type\WPInterface;
 * @since 0.0.1
 */

namespace WPGraphQL\TEC\Common\Type\WPInterface;

use GraphQL\Type\Definition\ResolveInfo;
use GraphQLRelay\Relay;
use WPGraphQL\AppContext;
use WPGraphQL\Registry\TypeRegistry;
use Tribe__Tickets__Tickets;
use WPGraphQL\TEC\Common\Type\WPObject\EventLinkedData;
use WPGraphQL\TEC\Tickets\Data\Factory;
use WPGraphQL\TEC\Tickets\Type\WPInterface\Ticket;

/**
 * Class - NodeWithJsonLd
 */
class NodeWithJsonLd {
	/**
	 * Name of the type.
	 *
	 * @var string Type name.
	 */
	public static $type = 'NodeWithJsonLd';

	/**
	 * Registers GraphQL Interface
	 *
	 * @param TypeRegistry $type_registry .
	 */
	public static function register_interface( TypeRegistry &$type_registry ): void {
		register_graphql_interface_type(
			self::$type,
			[
				'description' => __( 'Linked JSON-LD Data', 'wp-graphql-tec' ),
				'fields'      => [
					'linkedData' => [
						'type'        => EventLinkedData::$type,
						'description' => __( 'The JsonLD data for the event.', 'wp-graphql-tec' ),
						'resolve'     => function( $source ) {
							// TEC delivers this as an array with the eventId as the key.
							$value = tribe( 'tec.json-ld.event' )->get_data( $source->ID )[ $source->ID ];
							return $value ?: null;
						},
					],
				],
			]
		);
	}
}
