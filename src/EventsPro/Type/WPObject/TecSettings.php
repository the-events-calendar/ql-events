<?php
/**
 * Extends TecSettings
 *
 * @package WPGraphQL\TEC\Events\Type\WPObject
 * @since 0.0.1
 */

namespace WPGraphQL\TEC\EventsPro\Type\WPObject;

use WPGraphQL\TEC\Events\Type\Enum\EnabledViewsEnum;
use WPGraphQL\TEC\Events\Type\Enum\EventsTemplateEnum;
use WPGraphQL\TEC\EventsPro\Type\Enum\DistanceUnitEnum;

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
	public static function register_fields() : void {
		register_graphql_fields(
			self::$type,
			[
				'areSubsequentRecurrencesHidden'       => [
					'type'        => 'Boolean',
					'description' => __( 'Whether only the first instance of each recurring event is shown on list-style views', 'wp-graphql-tec' ),
					'resolve'     => fn() => ! empty( tribe_get_option( 'hideSubsequentRecurrencesDefault', '' ) ),
				],
				'isSubsequentRecurrencesToggleEnabled' => [
					'type'        => 'Boolean',
					'description' => __( 'Allow users to decide whether to show all instances of a recurring event on list-style views.', 'wp-graphql-tec' ),
					'resolve'     => fn() => ! empty( tribe_get_option( 'userToggleSubsequentRecurrences', '' ) ),
				],
				'recurrenceMaxMonthsBefore'            => [
					'type'        => 'Int',
					'description' => __( 'The number of months old a recurrening event instance must be before it is removed automatically by the plugin', 'wp-graphql-tec' ),
				],
				'recurrenceMaxMonthsAfter'             => [
					'type'        => 'Int',
					'description' => __( 'The number of months in advance for which recurring events are generated', 'wp-graphql-tec' ),
				],
				'defaultLocationDistance'              => [
					'type'        => 'Int',
					'description' => __( 'The default distance used to search by location', 'wp-graphql-tec' ),
					'resolve'     => fn() => tribe_get_option( 'geoloc_default_geofence', '' ) ?: null,
				],
				'defaultLocationUnit'                  => [
					'type'        => DistanceUnitEnum::$type,
					'description' => __( 'The default distance unit', 'wp-graphql-tec' ),
					'resolve'     => fn() => tribe_get_option( 'geoloc_default_unit', '' ) ?: null,
				],
				'defaultMobileView'                    => [
					'type'       => EnabledViewsEnum::$type,
					'descripton' => __( 'The default mobible event view.', 'wp-graphql-tec' ),
					'resolve'    => fn() => tribe_get_mobile_default_view() ?: null,
				],
				'areRelatedEventsHidden'               => [
					'type'       => 'Boolean',
					'descripton' => __( 'Whether Related Events should be hidden on the single event view.', 'wp-graphql-tec' ),
					'resolve'    => fn() => ! empty( tribe_get_option( 'hideRelatedEvents', '' ) ),
				],
				'areWeekendsHiddenOnWeekView'          => [
					'type'       => 'Boolean',
					'descripton' => __( 'Whether only weekdays should be displayed in the Week view.', 'wp-graphql-tec' ),
					'resolve'    => fn() => ! empty( tribe_get_option( 'week_view_hide_weekends', '' ) ),
				],
				'weekDayFormat'                        => [
					'type'       => 'String',
					'descripton' => __( 'The format to use for dates that show a month and year only. Used on month view.', 'wp-graphql-tec' ),
					'resolve'    => fn() => tribe_get_date_option( 'weekDayFormat', 'D jS' ),
				],
			]
		);
	}
}
