<?php
/**
 * Common utility functions.
 *
 * @package \WPGraphQL\TEC\Utils
 */

namespace WPGraphQL\TEC\Utils;

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
	public static function post_type_to_graphql_type( string $type ) : ?string{
		$registered_post_types = self::get_registered_post_types();

		return array_search( $type, $registered_post_types, true ) ?: null;
	}

	/**
	 * Helper function to convert a GraphQL type name to a post type name.
	 *
	 * @param string $type The GraphQL type.
	 */
	public static function graphql_type_to_post_type( string $type ) : ?string {
		$registered_post_types = self::get_registered_post_types();
		$type = ucfirst( $type );

		return $registered_post_types[ $type ] ?? null;
	}

	/**
	 * Returns an array key-value pair of registed TEC Object Types and their corresponding postType.
	 * 
	 * Example: `[ 'Event' => 'tribe_events' ]
	 *
	 * @return array
	 */
	public static function get_registered_post_types() : array {
		return [
			WPObject\Event::$type => 'tribe_events',
			WPObject\Organizer::$type => 'tribe_organizer',
			WPObject\Venue::$type => 'tribe_venue',
		];
	}

	/**
	 * Helper function to check if the post type is a TEC type.
	 *
	 * @param string $post_type the post type name.
	 */
	public static function is_tec_post_type( string $post_type ) : ?bool {
		$registered_post_types = self::get_registered_post_types();

		return in_array( $post_type, $registered_post_types, true );
	}

	/**
	 * Checks if source string starts with target string.
	 *
	 * @param string $haystack .
	 * @param string $needle .
	 */
	public static function starts_with( string $haystack, string $needle) : bool {
		$length = strlen( $needle );
		return substr( $haystack, 0, $length) === $needle;
	}

	/**
	 * Checks if source string ends with target string.
	 *
	 * @param string $haystack .
	 * @param string $needle .
	 */
	public static function ends_with( string $haystack, string $needle) : bool {
		$length = strlen( $needle );
		if( 0 === $length ){
			return true;
		}

		return substr( $haystack, -$length ) === $needle;
	}
}
