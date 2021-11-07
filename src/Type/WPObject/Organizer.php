<?php
/**
 * GraphQL Object Type - Organizer
 *
 * @package WPGraphQL\TEC\Type\Object
 * @since 0.0.1
 */

namespace WPGraphQL\TEC\Type\WPObject;

/**
 * Class - Organizer
 */
class Organizer {

	/**
	 * Name of the type.
	 *
	 * @var string Type name.
	 */
	public static $type = 'Organizer';
	/**
	 * {@inheritDoc}
	 */
	public static function register_fields() : void {
		self::register_core_fields();
	}

	/**
	 * Register the fields used by TEC Core plugin.
	 */
	public static function register_core_fields() : void {
		register_graphql_fields(
			self::$type,
			[
				'email'      => [
					'type'        => 'String',
					'args'        => [
						'antispambot' => [
							'type'        => 'Boolean',
							'description' => __( 'Whether the email should pass through the `antispambot` function or not. Defaults to `true`.', 'wp-graphql-tec' ),
						],
					],
					'description' => __( 'The organizer email address.', 'wp-graphql-tec' ),
					'resolve'     => function( $source, array $args ) : ?string {
						$antispambot = $args['antispambot'] ?? true;
						return tribe_get_organizer_email( $source->ID, $antispambot ) ?: null;
					},
				],
				'linkedData' => [
					'type'        => OrganizerLinkedData::$type,
					'description' => __( 'The JsonLD data for the organizer.', 'wp-graphql-tec' ),
				],
				'phone'      => [
					'type'        => 'String',
					'description' => __( 'The organizer phone number.', 'wp-graphql-tec' ),
				],
				'website'    => [
					'type'        => 'String',
					'description' => __( 'The organizer website.', 'wp-graphql-tec' ),
				],
			]
		);
	}
}
