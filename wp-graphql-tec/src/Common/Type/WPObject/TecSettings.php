<?php
/**
 * GraphQL Object Type - TecSettings
 *
 * @package WPGraphQL\TEC\Common\Type\WPObject * @since 0.0.1
 */

namespace WPGraphQL\TEC\Common\Type\WPObject;

use Tribe__Date_Utils;
use Tribe__Settings_Manager;
use WPGraphQL\TEC\Common\Type\Enum\TimezoneModeEnum;

/**
 * Class - TecSettings
 */
class TecSettings {
	/**
	 * Name of the type.
	 *
	 * @var string Type name.
	 */
	public static $type = 'TecSettings';

	/**
	 * {@inheritDoc}
	 */
	public static function register_type() : void {
		register_graphql_object_type(
			self::$type,
			[
				'description' => __( 'The Events Calendar site settings', 'wp-graphql-tec' ),
				'fields'      => [
					'datepickerFormat' => [
						'type'       => 'String',
						'descripton' => __( 'The date format used for elements with minimal space, such as in datepickers', 'wp-graphql-tec' ),
						'resolve'    => fn() => Tribe__Date_Utils::datepicker_formats( tribe_get_option( 'datepickerFormat' ) ),
					],
					'timezoneMode'     => [
						'type'       => TimezoneModeEnum::$type,
						'descripton' => __( 'Time zone mode', 'wp-graphql-tec' ),
						'resolve'    => fn( $source ) => $source['tribe_events_timezone_mode'] ?? null,
					],
				],
			]
		);

		register_graphql_fields(
			'RootQuery',
			[
				'tecSettings' => [
					'type'        => self::$type,
					'description' => __( 'TEC site settings.', 'wp-graphql-tec' ),
					'resolve'     => function() {
						return Tribe__Settings_Manager::get_options();
					},
				],
			]
		);
	}
}
