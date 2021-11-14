<?php
/**
 * Ticket Helper methods for the resolver Factory.
 *
 * @package WPGraphQL\TEC\Tickets\Data
 * @since 0.0.1
 */

namespace WPGraphQL\TEC\Tickets\Data;

use WPGraphQL\AppContext;
use GraphQL\Type\Definition\ResolveInfo;
use WP_Post;
use WPGraphQL\TEC\Tickets\Type\Enum\TicketTypeEnum;
use WPGraphQL\TEC\Tickets\Type\Input\IntRangeInput;
use WPGraphQL\TEC\Utils\Utils;

/**
 * Class - Ticket Helper
 */
class TicketHelper {
	/**
	 * Modifies the default connection configuration.
	 *
	 * @param array $config .
	 */
	public static function get_connection_config( array $config ) : array {
		$config['connectionArgs'] = array_merge(
			$config['connectionArgs'] ?? [],
			self::get_connection_args(),
		);

		$type = $config['toType'];

		$config['resolve'] = function( $source, array $args, AppContext $context, ResolveInfo $info ) use ( $type ) {
			$args = self::map_args( $args );

			return Factory::resolve_tickets_connection( $source, $args, $context, $info, $type );
		};

		return $config;
	}

	/**
	 * Gets an array of connection args to register to the Ticket Query.
	 */
	public static function get_connection_args() : array {
		return [
			'attendeesBetween' => [
				'type'        => IntRangeInput::$type,
				'description' => __( 'Filter by tickets that have a number of attendees between two values', 'wp-graphql-tec' ),
			],
			'attendeesMax'     => [
				'type'        => 'Int',
				'description' => __( 'Filter by tickets that have a maximum number of attendees', 'wp-graphql-tec' ),
			],
			'attendeesMin'     => [
				'type'        => 'Int',
				'description' => __( 'Filter by tickets that have a minimum number of attendees', 'wp-graphql-tec' ),
			],
			'availableFrom'    => [
				'type'        => 'String',
				'description' => __( 'Filter tickets by their availability start date. Accepts a UTC date or timestamp', 'wp-graphql-tec' ),
			],
			'availableUntil'   => [
				'type'        => 'String',
				'description' => __( 'Filter tickets by their availability end date. Accepts a UTC date or timestamp', 'wp-graphql-tec' ),
			],
			'capacityBetween'  => [
				'type'        => IntRangeInput::$type,
				'description' => __( 'Filter by tickets that have a capacity between two values', 'wp-graphql-tec' ),
			],
			'capacityMax'      => [
				'type'        => 'Int',
				'description' => __( 'Filter by tickets that have a maximum capacity', 'wp-graphql-tec' ),
			],
			'capacityMin'      => [
				'type'        => 'Int',
				'description' => __( 'Filter by tickets that have a minimum capacity', 'wp-graphql-tec' ),
			],
			'checkedInBetween' => [
				'type'        => IntRangeInput::$type,
				'description' => __( 'Filter by tickets that have a number of checked-in attendees between two values', 'wp-graphql-tec' ),
			],
			'checkedInMax'     => [
				'type'        => 'Int',
				'description' => __( 'Filter by tickets that have a maximum number of checked-in attendees', 'wp-graphql-tec' ),
			],
			'checkedInMin'     => [
				'type'        => 'Int',
				'description' => __( 'Filter by tickets that have a minimum number of checked-in attendees', 'wp-graphql-tec' ),
			],
			'currencyCode'     => [
				'type'        => [ 'list_of' => 'String' ],
				'description' => __( 'Filter tickets by their provider currency codes. Accepts a 3-letter currency_codes, or an array of codes.', 'wp-graphql-tec' ),
			],
			'eventIn'          => [
				'type'        => [ 'list_of' => 'Int' ],
				'description' => __( 'Filters tickets attached to a specific post ID or array of IDs.', 'wp-graphql-tec' ),
			],
			'eventNotIn'       => [
				'type'        => [ 'list_of' => 'Int' ],
				'description' => __( 'Filters tickets not attached to a specific post ID or array of IDs.', 'wp-graphql-tec' ),
			],
			'eventStatus'      => [
				'type'        => [ 'list_of' => 'String' ],
				'description' => __( 'Filters tickets by their associated post`s status or array of statii.', 'wp-graphql-tec' ),
			],
			'hasAttendeeMeta'  => [
				'type'        => 'Boolean',
				'description' => __( 'Filters tickets depending on them having additional information available and active or not based on the provided value.', 'wp-graphql-tec' ),
			],
			'isActive'         => [
				'type'        => 'Boolean',
				'description' => __( 'Filters tickets by if they are currently available or available in the future based on the provided value', 'wp-graphql-tec' ),
			],
			'isAvailable'      => [
				'type'        => 'Boolean',
				'description' => __( 'Filters tickets by if they are currently available or available in the future based on the provided value.', 'wp-graphql-tec' ),
			],
			'provider'         => [
				'type'        => [ 'list_of' => TicketTypeEnum::$type ],
				'description' => __( 'Filters tickets by if they are currently available or available in the future based on the provided value.', 'wp-graphql-tec' ),
			],
		];
	}

	/**
	 * Converts where arg keys to those understood by TEC.
	 *
	 * @param array $args The GraphQL query where args.
	 */
	public static function map_args( array $args ) : array {
		if ( isset( $args['where']['provider'] ) ) {
			$provider = $args['where']['provider'];
			$provider = is_array( $provider ) ? $provider : [ $provider ];
			$provider = array_map( [ Utils::class, 'get_et_provider_for_type' ], $provider );
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
