<?php
/**
 * Adds filters that modify core schema.
 *
 * @package \WPGraphQL\TEC\EventsPro
 * @since   0.0.1
 */

namespace WPGraphQL\TEC\EventsPro;

use WPGraphQL\AppContext;
use WPGraphQL\TEC\EventsPro\Data\EventHelper;
use WPGraphQL\TEC\EventsPro\Model\Event;
use WPGraphQL\TEC\EventsPro\Model\Venue;
use WPGraphQL\TEC\EventsPro\Type\WPObject\Event as WPObjectEvent;
use WPGraphQL\TEC\EventsPro\Type\WPObject\Venue as WPObjectVenue;
use WPGraphQL\TEC\EventsPro\Type\WPObject\VenueCoordinates;
use WPGraphQL\TEC\Interfaces\Hookable;

/**
 * Class - CoreSchemaFilters
 */
class CoreSchemaFilters implements Hookable {
	/**
	 * {@inheritdoc}
	 */
	public static function register_hooks() : void {

		// Extend models.
		add_filter( 'graphql_tec_event_model_fields', [ Event::class, 'extend' ], 10, 2 );
		add_filter( 'graphql_tec_venue_model_fields', [ Venue::class, 'extend' ], 10, 2 );

		add_filter( 'graphql_tec_events_connection_args', [ EventHelper::class, 'add_where_args_to_events_connection' ] );
		// @todo refactor resolver to use tribe repository.
		// add_filter( 'graphql_tec_venues_connection_args', [ EventHelper::class, 'add_where_args_to_events_connection' ] );
	}
}
