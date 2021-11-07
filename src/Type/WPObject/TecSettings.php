<?php
/**
 * GraphQL Object Type - TecSettings
 *
 * @package WPGraphQL\TEC\Type\Object
 * @since 0.0.1
 */

namespace WPGraphQL\TEC\Type\WPObject;

use Tribe__Date_Utils;
use Tribe__Settings_Manager;
use WPGraphQL\TEC\Type\Enum\EnabledViewsEnum;
use WPGraphQL\TEC\Type\Enum\EventsTemplateEnum;
use WPGraphQL\TEC\Type\Enum\TimezoneModeEnum;

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
		self::register_core_fields();
	}

	/**
	 * Register the fields used by TEC Core plugin.
	 */
	public static function register_core_fields() : void {
		register_graphql_object_type(
			self::$type,
			[
				'description' => __( 'The Events Calendar site settings', 'wp-graphql-tec' ),
				'fields'      => [
					'afterHTML'                  => [
						'type'       => 'String',
						'descripton' => __( 'Additional code added after the event template.', 'wp-graphql-tec' ),
						'resolve'    => function () : ?string {
							ob_start();
							tribe_events_after_html();
							return ob_get_clean() ?: null;
						},
					],
					'beforeHTML'                 => [
						'type'       => 'String',
						'descripton' => __( 'Additional code added before the event template.', 'wp-graphql-tec' ),
						'resolve'    => function () : ?string {
							ob_start();
							tribe_events_before_html();
							return ob_get_clean() ?: null;
						},
					],
					'datepickerFormat'           => [
						'type'       => 'String',
						'descripton' => __( 'The date format used for elements with minimal space, such as in datepickers', 'wp-graphql-tec' ),
						'resolve'    => fn() => Tribe__Date_Utils::datepicker_formats( tribe_get_option( 'datepickerFormat' ) ),
					],
					'dateTimeSeparator'          => [
						'type'       => 'String',
						'descripton' => __( 'The separator that will be placed between the date and time, when both are shown.', 'wp-graphql-tec' ),
						'resolve'    => fn() => tribe_get_datetime_separator(),
					],
					'dateWithoutYearFormat'      => [
						'type'       => 'String',
						'descripton' => __( 'The format to use for displaying dates with the year. Used when displaying a date without a year. Used when showing an event from the current year.', 'wp-graphql-tec' ),
						'resolve'    => fn() => tribe_get_date_format( false ),
					],
					'dateWithYearFormat'         => [
						'type'       => 'String',
						'descripton' => __( 'The format to use for displaying dates with the year. Used when displaying a date in a future year.', 'wp-graphql-tec' ),
						'resolve'    => fn() => tribe_get_date_format( true ),
					],
					'defaultCurrencySymbol'      => [
						'type'        => 'String',
						'description' => __( 'Default currency symbol.', 'wp-graphql-tec' ),
					],
					'defaultView'                => [
						'type'       => EnabledViewsEnum::$type,
						'descripton' => __( 'The default event view.', 'wp-graphql-tec' ),
						'resolve'    => fn( $source ) => $source['viewOption'] ?: null,
					],
					'disableMetaboxCustomFields' => [
						'type'        => 'Boolean',
						'description' => __( 'Enable WordPress Custom Fields on events in the classic editor.', 'wp-graphql-tec' ),
						'resolve'     => fn( $source) => $source['disable_metabox_custom_fields'] ?? null,
					],
					'disableSearchBar'           => [
						'type'       => 'Boolean',
						'descripton' => __( 'Whether to use classic header instead.', 'wp-graphql-tec' ),
						'resolve'    => fn() => tribe_get_option( 'tribeDisableTribeBar', false ),
					],
					'eventsTemplate'             => [
						'type'       => EventsTemplateEnum::$type,
						'descripton' => __( 'The Events template.', 'wp-graphql-tec' ),
						'resolve'    => fn( $source ) => $source['tribeEventsTemplate'] ?: '',
					],
					'enabledViews'               => [
						'type'       => [ 'list_of' => EnabledViewsEnum::$type ],
						'descripton' => __( 'The event views enabled.', 'wp-graphql-tec' ),
						'resolve'    => fn( $source ) => $source['tribeEnableViews'] ?: null,
					],
					'embedGoogleMaps'            => [
						'type'        => 'Boolean',
						'description' => __( 'Whether to enable maps for events and venues.', 'wp-graphql-tec' ),
					],
					'embedGoogleMapsZoom'        => [
						'type'        => 'Integer',
						'description' => __( 'Google Maps default zoom level', 'wp-graphql-tec' ),
						'resolve'     => fn( $source ) => $source['embedGoogleMapsZoom'] ?: null,
					],
					'eventsSlug'                 => [
						'type'        => 'String',
						'description' => __( 'Events URL slug/', 'wp-graphql-tec' ),
					],
					// @todo Google Maps API Key
					'monthEventAmount'           => [
						'type'       => 'Integer',
						'descripton' => __( 'The number of events to display per day in month view. -1 for unlimited.', 'wp-graphql-tec' ),
					],
					'multiDayCutoff'             => [
						'type'        => 'String',
						'description' => __( 'End of day cutoff.', 'wp-graphql-tec' ),
					],
					'monthAndYearFormat'         => [
						'type'       => 'String',
						'descripton' => __( 'The format to use for dates that show a month and year only. Used on month view.', 'wp-graphql-tec' ),
						'resolve'    => fn() => tribe_get_date_option( 'monthAndYearFormat', 'F Y' ),
					],
					'postsPerPage'               => [
						'type'        => 'Int',
						'description' => __( 'The number of events per page on the List View. Does not affect other views.', 'wp-graphql-tec' ),
					],
					'reverseCurrencyPosition'    => [
						'type'        => 'Boolean',
						'description' => __( 'If the currency symbol shoud appear after the value.', 'wp-graphql-tec' ),
					],
					'showComments'               => [
						'type'        => 'Boolean',
						'description' => __( 'Enable comments on event pages.', 'wp-graphql-tec' ),
					],
					'showEventsInMainLoop'       => [
						'type'        => 'Boolean',
						'description' => __( 'Show events with the site\'s other posts. When this box is checked, events will also continue to appear on the default events page.', 'wp-graphql-tec' ),
					],
					'singleEventSlug'            => [
						'type'        => 'String',
						'description' => __( 'Single event URL slug.', 'wp-graphql-tec' ),
					],
					'timeRangeSeparator'         => [
						'type'       => 'String',
						'descripton' => __( 'The separator that will be used between the start and end time of an event.', 'wp-graphql-tec' ),
						'resolve'    => fn() => tribe_get_option( 'timeRangeSeparator', ' - ' ),
					],
					'timezoneMode'               => [
						'type'       => TimezoneModeEnum::$type,
						'descripton' => __( 'Time zone mode', 'wp-graphql-tec' ),
						'resolve'    => fn( $source ) => $source['tribe_events_timezone_mode'] ?? null,
					],
					'timezoneShowZone'           => [
						'type'       => 'Boolean',
						'descripton' => __( 'Whether to display the time zone to the end of the scheduling information.', 'wp-graphql-tec' ),
						'resolve'    => fn( $source ) => $source['tribe_events_timezones_show_zone'] ?? null,
					],
				],
			]
		);

		register_graphql_fields(
			'RootQuery',
			[
				'tecSettings' => [
					'type'        => self::$type,
					'description' => __( 'The Events Calendar site settings.', 'wp-graphql-tec' ),
					'resolve'     => function() {
						return Tribe__Settings_Manager::get_options();
					},
				],
			]
		);
	}
}
