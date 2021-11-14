<?php
/**
 * Event Helper methods for the resolver Factory.
 *
 * @package WPGraphQL\TEC\Events\Data
 * @since 0.0.1
 */

namespace WPGraphQL\TEC\Events\Data;

use WPGraphQL\AppContext;
use GraphQL\Type\Definition\ResolveInfo;
use WP_Post;
use WP_Term;
use WPGraphQL\TEC\Events\Type\Input\CostFilterInput;
use WPGraphQL\TEC\Events\Type\Input\DateAndTimezoneInput;
use WPGraphQL\TEC\Events\Type\Input\DateRangeAndTimezoneInput;

/**
 * Class - Event Helper
 */
class EventHelper {
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
			$args = self::map_args( $args );

			return Factory::resolve_events_connection( $source, $args, $context, $info );
		};

		return $config;
	}

	/**
	 * Gets an array of connection args to register to the Event Query.
	 */
	public static function get_connection_args() : array {
		// @todo add date args.
		return [
			'cost'               => [
				'type'        => CostFilterInput::$type,
				'description' => __( 'Filters events that have a cost relative to the given value based on the operator. Providing the symbol parameter should limit event results to only those events whose cost is relative to the value AND the currency symbol matches.', 'wp-graphql-tec' ),
			],
			'endsAfter'          => [
				'type'        => DateAndTimezoneInput::$type,
				'description' => __( 'Filters events whose end date occurs after the provided date (exclusive).', 'wp-graphql-tec' ),
			],
			'endsBefore'         => [
				'type'        => DateAndTimezoneInput::$type,
				'description' => __( 'Filters events whose end date occurs before the provided date (inclusive).', 'wp-graphql-tec' ),
			],
			'endsBetween'        => [
				'type'        => DateRangeAndTimezoneInput::$type,
				'description' => __( 'Filters events whose whose end date occurs between a set of dates (inclusive). ', 'wp-graphql-tec' ),
			],
			'endsOnOrBefore'     => [
				'type'        => DateAndTimezoneInput::$type,
				'description' => __( 'Filters events whose end date occurs on or before the provided date (exclusive).', 'wp-graphql-tec' ),
			],
			'eventDateOverlaps'  => [
				'type'        => DateRangeAndTimezoneInput::$type,
				'description' => __( 'Filters events whose duration overlaps a given Start and End date (inclusive).', 'wp-graphql-tec' ),
			],
			'isAllDay'           => [
				'type'        => 'Boolean',
				'description' => __( 'Only return events that match the provided all day state.', 'wp-graphql-tec' ),
			],
			'isFeatured'         => [
				'type'        => 'Boolean',
				'description' => __( 'Only return events that match the provided featured state.', 'wp-graphql-tec' ),
			],
			'isHidden'           => [
				'type'        => 'Boolean',
				'description' => __( 'Only return events that match the provided hidden state.', 'wp-graphql-tec' ),
			],
			'isMultiday'         => [
				'type'        => 'Boolean',
				'description' => __( 'Only return events that match the provided multi day state.', 'wp-graphql-tec' ),
			],
			'isSticky'           => [
				'type'        => 'Boolean',
				'description' => __( 'Only return events that match the provided sticky state.', 'wp-graphql-tec' ),
			],
			'runsBetween'        => [
				'type'        => DateRangeAndTimezoneInput::$type,
				'description' => __( 'Filters events to include only those events whose start and end dates are between a set of dates. ', 'wp-graphql-tec' ),
			],
			'startsAfter'        => [
				'type'        => DateAndTimezoneInput::$type,
				'description' => __( 'Filters events whose start date occurs after the provided date (exclusive).', 'wp-graphql-tec' ),
			],
			'startsBefore'       => [
				'type'        => DateAndTimezoneInput::$type,
				'description' => __( 'Filters events whose start date occurs before the provided date (exclusive).', 'wp-graphql-tec' ),
			],
			'startsBetween'      => [
				'type'        => DateRangeAndTimezoneInput::$type,
				'description' => __( 'Filters events whose whose start date occurs between a set of dates (inclusive). ', 'wp-graphql-tec' ),
			],
			'startsOnDate'       => [
				'type'        => DateAndTimezoneInput::$type,
				'description' => __( 'Filters events to include only those that start on a specific date.', 'wp-graphql-tec' ),
			],
			'startsOnOrAfter'    => [
				'type'        => DateAndTimezoneInput::$type,
				'description' => __( 'Filters events whose start date occurs on or after the provided date (inclusive).', 'wp-graphql-tec' ),
			],
			'timezone'           => [
				'type'        => 'String',
				'description' => __( 'Filters events to those set to provided timezone string or UTC offset.', 'wp-graphql-tec' ),
			],
			// Event Category.
			'eventCategoryId'    => [
				'type'        => 'Int',
				'description' => __( 'Category ID.', 'wp-graphql-tec' ),
			],
			'eventCategoryIn'    => [
				'type'        => [ 'list_of' => 'ID' ],
				'description' => __( 'Array of category IDs, used to display objects from one category OR another.', 'wp-graphql-tec' ),
			],
			'eventCategoryName'  => [
				'type'        => 'String',
				'description' => __( 'Use Event Category slug.', 'wp-graphql-tec' ),
			],
			'eventCategoryNotIn' => [
				'type'        => [ 'list_of' => 'ID' ],
				'description' => __( 'Array of category IDs, used to display objects from one category OR another.', 'wp-graphql-tec' ),
			],
			// Organizer.
			'organizerId'        => [
				'type'        => 'Int',
				'description' => __( 'Organizer ID.', 'wp-graphql-tec' ),
			],
			'organizerIn'        => [
				'type'        => [ 'list_of' => 'ID' ],
				'description' => __( 'Array of organizer IDs, used to display objects from one organizer OR another.', 'wp-graphql-tec' ),
			],
			'organizerName'      => [
				'type'        => 'String',
				'description' => __( 'Use Organizer slug.', 'wp-graphql-tec' ),
			],
			'organizerNotIn'     => [
				'type'        => [ 'list_of' => 'ID' ],
				'description' => __( 'Array of organizer IDs, used to display objects from one organizer OR another.', 'wp-graphql-tec' ),
			],
			// Venue.
			'venueId'            => [
				'type'        => 'Int',
				'description' => __( 'Venue ID.', 'wp-graphql-tec' ),
			],
			'venueIn'            => [
				'type'        => [ 'list_of' => 'ID' ],
				'description' => __( 'Array of venue IDs, used to display objects from one venue OR another.', 'wp-graphql-tec' ),
			],
			'venueName'          => [
				'type'        => 'String',
				'description' => __( 'Use Venue slug.', 'wp-graphql-tec' ),
			],
			'venueNotIn'         => [
				'type'        => [ 'list_of' => 'ID' ],
				'description' => __( 'Array of venue IDs, used to display objects from one venue OR another.', 'wp-graphql-tec' ),
			],
		];
	}

	/**
	 * Converts where arg keys to those understood by TEC.
	 *
	 * @param array $args The GraphQL query where args.
	 */
	public static function map_args( array $args ) : array {
		if ( empty( $args['where'] ) ) {
			return $args;
		}

		foreach ( $args['where'] as $key => &$value ) {
			if ( empty( $value ) ) {
				continue;
			}

			switch ( $key ) {
				case 'eventCategoryName':
					$term = get_term_by( 'slug', $value, 'tribe_events_cat' );
					if ( $term instanceof WP_Term ) {
						$args['where']['eventCategoryId'] = $term->term_taxonomy_id;
					}
					unset( $args['where'][ $key ] );
					break;
				case 'organizerName':
				case 'venueName':
					self::map_name_to_post_id( $args['where'], $key );
					break;
			}
		}

		return $args;
	}

	/**
	 * Converts {type}Name inout to {type}Id.
	 *
	 * @param array  $where_args .
	 * @param string $key the current where argument.
	 */
	public static function map_name_to_post_id( array &$where_args, string $key ) : void {
		$type = substr( $key, 0, -4 );

		$post = get_page_by_path( $where_args[ $key ], OBJECT, 'tribe_' . $type );

		if ( $post instanceof WP_Post ) {
			$where_args[ $type . 'Id' ] = $post->ID;
		}

		unset( $where_args[ $key ] );
	}
}
