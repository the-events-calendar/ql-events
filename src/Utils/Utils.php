<?php
/**
 * Common utility functions.
 *
 * @package \WPGraphQL\TEC\Utils
 */

namespace WPGraphQL\TEC\Utils;

use WPGraphQL\TEC\TEC;
use WPGraphQL\TEC\Type\WPObject;
/**
 * Class - Utils
 */
class Utils {

	/**
	 * Helper function to convert a post type name to the GraphQL type name.
	 *
	 * @param string $type The post type.
	 */
	public static function post_type_to_graphql_type( string $type ) : ?string {
		$registered_post_types = self::get_registered_post_types();

		return $registered_post_types[ $type ] ?? null;
	}

	/**
	 * Helper function to convert a GraphQL type name to a post type name.
	 *
	 * @param string $type The GraphQL type.
	 */
	public static function graphql_type_to_post_type( string $type ) : ?string {
		$registered_post_types = self::get_registered_post_types();
		$type                  = ucfirst( $type );

		return (string) array_search( $type, $registered_post_types, true ) ?: null;
	}

	/**
	 * Returns an array key-value pair of registered post type and their corresponding GraphQL object type.
	 *
	 * Example: `[ 'tribe_events' => 'Event' ]
	 *
	 * @return array
	 */
	public static function get_registered_post_types() : array {
		$post_types = array_merge(
			TEC::is_tec_loaded() ? self::get_tec_types() : [],
			TEC::is_et_loaded() ? self::get_et_types() : [],
		);

		return apply_filters( 'graphql_tec_post_types', $post_types );
	}

	/**
	 * Returns an array key-value pair of registered The Events Calendar post types and their corresponding GraphQL object type.
	 *
	 * Example: `[ 'tribe_events' => 'Event' ]
	 */
	public static function get_tec_types() : array {
		return [
			WPObject\Event::$wp_type     => WPObject\Event::$type,
			WPObject\Organizer::$wp_type => WPObject\Organizer::$type,
			WPObject\Venue::$wp_type     => WPObject\Venue::$type,
		];
	}

	/**
	 * Returns an array key-value pair of registered Event Ticket post types and their corresponding GraphQL object type.
	 *
	 * Example: `[ 'tribe_events' => 'Event' ]
	 */
	public static function get_et_types() : array {
		return [
			'tec_tc_ticket'      => 'TcTicket',
			'tribe_rsvp_tickets' => 'RsvpTicket',
			'tribe_tpp_tickets'  => 'PayPalTicket',
		];
	}

	/**
	 * Helper function to check if the post type is a TEC type.
	 *
	 * @param string $post_type the post type name.
	 */
	public static function is_tec_post_type( string $post_type ) : ?bool {
		$registered_post_types = self::get_registered_post_types();

		return in_array( $post_type, array_keys( $registered_post_types ), true );
	}

	/**
	 * Checks if source string starts with target string.
	 *
	 * @param string $haystack .
	 * @param string $needle .
	 */
	public static function starts_with( string $haystack, string $needle ) : bool {
		$length = strlen( $needle );
		return substr( $haystack, 0, $length ) === $needle;
	}

	/**
	 * Checks if source string ends with target string.
	 *
	 * @param string $haystack .
	 * @param string $needle .
	 */
	public static function ends_with( string $haystack, string $needle ) : bool {
		$length = strlen( $needle );
		if ( 0 === $length ) {
			return true;
		}

		return substr( $haystack, -$length ) === $needle;
	}
}
