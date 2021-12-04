<?php
/**
 * Extends the Event Model class
 *
 * @package \WPGraphQL\TEC\EventsPro\Model
 * @since   0.0.1
 */

namespace WPGraphQL\TEC\EventsPro\Model;

use WP_Post;

/**
 * Class - Event
 */
class Event {
	/**
	 * Extends the WPGraphQL Model.
	 *
	 * @param array   $fields The fields registered to the model.
	 * @param WP_Post $data The model data.
	 */
	public static function extend( array $fields, WP_Post $data ) : array {
		$fields['isRecurring'] = function() use ( $data ) : bool {
			return ! empty( $data->is_recurring );
		};

		$fields['recurrenceText'] = function() use ( $data ) : ?string {
			return tribe_get_recurrence_text( $data->ID ) ?: null;
		};

		$fields['recurrenceIcalLink'] = function() use ( $data ) : ?string {
			return tribe_get_recurrence_ical_link( $data->ID ) ?: null;
		};

		$fields['startDates'] = function() use ( $data ) : ?array {
			return tribe_get_recurrence_start_dates( $data->ID ) ?: null;
		};

		$fields['permalinkAll'] = function() use ( $data ) : ?string {
			return ! empty( $data->permalink_all ) ? $data->permalink_all : null;
		};

		$fields['custom'] = function() use ( $data ) : ?array {
			return tribe_get_custom_fields( $data->ID ) ?: null;
		};

		return $fields;
	}
}
