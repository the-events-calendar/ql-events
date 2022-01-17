<?php
/**
 * Extends the Venue Model class
 *
 * @package \WPGraphQL\TEC\EventsPro\Model
 * @since   0.0.1
 */

namespace WPGraphQL\TEC\EventsPro\Model;

use WPGraphQL\TEC\Events\Model\Venue as VenueModel;
use Tribe__Events__Pro__Geo_Loc as Geolocation;
use WP_Post;

/**
 * Class - Venue
 */
class Venue {
	/**
	 * Extends the WPGraphQL Model.
	 *
	 * @param array   $fields The fields registered to the model.
	 * @param WP_Post $data The model data.
	 */
	public static function extend( array $fields, WP_Post $data ) : array {
		$fields['geolocation'] = function() use ( $data ) {
			return ! empty( $data->geolocation ) ? $data->geolocation : null;
		};

		return $fields;
	}
}
