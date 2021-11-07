<?php
/**
 * Event Helper methods for the resolver Factory.
 *
 * @package WPGraphQL\TEC\Data
 * @since 0.0.1
 */

namespace WPGraphQL\TEC\Data;

use WPGraphQL\AppContext;
use GraphQL\Type\Definition\ResolveInfo;

/**
 * Class - Event Helper
 */
class OrganizerHelper {
	/**
	 * Modifies the default connection configuration.
	 *
	 * @param array $config .
	 */
	public static function get_connection_config( array $config ) : array {
		$config['connectionArgs'] = array_merge(
			$config['connectionArgs'],
			self::get_connection_args(),
		);

		$config['resolve'] = function( $source, array $args, AppContext $context, ResolveInfo $info ) {
			return Factory::resolve_organizers_connection( $source, $args, $context, $info );
		};

		return $config;
	}

	/**
	 * Gets an array of connection args to register to the Event Query.
	 */
	public static function get_connection_args() : array {
		return [
			'eventId'     => [
				'type'        => 'Int',
				'description' => __( 'Only venues linked to this event post ID.', 'wp-graphql-tec' ),
			],
			'hasEvents'   => [
				'type'        => 'Boolean',
				'description' => __( 'Only venues that have events.', 'wp-graphql-tec' ),
			],
			'hasNoEvents' => [
				'type'        => 'Boolean',
				'description' => __( 'Only venues that have no events.', 'wp-graphql-tec' ),
			],
		];
	}
}
