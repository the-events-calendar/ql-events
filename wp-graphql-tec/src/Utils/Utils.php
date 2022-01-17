<?php
/**
 * Common utility functions.
 *
 * @package \WPGraphQL\TEC\Utils
 */

namespace WPGraphQL\TEC\Utils;

use WPGraphQL\TEC\TEC;
use WPGraphQL\TEC\Events\Type\WPObject as EventsObject;
use WPGraphQL\TEC\Tickets\Type\WPObject as TicketsObject;
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
	 * Returns an array or registered taxonomy types and their corresponding GraphQL Object type
	 *
	 * @return array
	 */
	public static function get_registered_taxonomies() : array {
		$taxonomy_types = array_merge(
			TEC::is_tec_loaded() ? [ 'tribe_events_cat' => 'EventCategory' ] : [],
			[]
		);

		return apply_filters( 'grapqhl_tec_taxonomy_types', $taxonomy_types );
	}

	/**
	 * Returns an array key-value pair of registered The Events Calendar post types and their corresponding GraphQL object type.
	 *
	 * Example: `[ 'tribe_events' => 'Event' ]
	 */
	public static function get_tec_types() : array {
		return [
			EventsObject\Event::$wp_type     => EventsObject\Event::$type,
			EventsObject\Organizer::$wp_type => EventsObject\Organizer::$type,
			EventsObject\Venue::$wp_type     => EventsObject\Venue::$type,
		];
	}

	/**
	 * Returns an array key-value pair of registered Event Tickets post types and their corresponding GraphQL object type.
	 *
	 * Example: `[ 'tribe_events' => 'Event' ]
	 */
	public static function get_et_types() : array {
		return self::get_et_ticket_types()
			+ self::get_et_attendee_types()
			+ self::get_et_order_types();
	}

	/**
	 * Returns an array key-value pair of registered Event Ticket ticket post types and their corresponding GraphQL object type.
	 *
	 * Example: `[ 'tribe_events' => 'Event' ]
	 */
	public static function get_et_ticket_types() : array {
		return [
			TicketsObject\RsvpTicket::$wp_type => TicketsObject\RsvpTicket::$type,
			'tec_tc_ticket'                    => 'TcTicket',
			'tribe_tpp_tickets'                => 'PayPalTicket',
		];
	}

	/**
	 * Returns an array key-value pair of registered Event Tickets attendee post types and their corresponding GraphQL object type.
	 *
	 * Example: `[ 'tribe_events' => 'Event' ]
	 */
	public static function get_et_attendee_types() : array {
		return [
			'tribe_rsvp_attendees' => 'RsvpAttendee',
			'tribe_tpp_attendees'  => 'PayPalAttendee',
			'tec_tc_attendee'      => 'TcAttendee',
		];
	}
	/**
	 * Returns an array key-value pair of registered Event Tickets order post types and their corresponding GraphQL object type.
	 *
	 * Example: `[ 'tribe_events' => 'Event' ]
	 */
	public static function get_et_order_types() : array {
		return [
			'tec_tc_order'     => 'TcOrder',
			'tribe_tpp_orders' => 'PayPalOrder',
		];
	}

	/**
	 * Gets the name of the TEC Ticket provider for given type.
	 *
	 * @param string $type Either post type (e.g. 'tribe_rsvp_tickets) or model type (e.g. 'RsvpTicket' ).
	 */
	public static function get_et_provider_for_type( string $type ) : string {
		$provider = 'default';
		switch ( $type ) {
			case 'tribe_rsvp_tickets':
			case 'RsvpTicket':
			case 'tribe_rsvp_attendees':
			case 'RsvpAttendee':
				$provider = 'rsvp';
				break;
			case 'tribe_tpp_tickes':
			case 'PayPalTicket':
			case 'tribe_tpp_orders':
			case 'PayPalOrder':
			case 'tribe_tpp_attendees':
			case 'PayPalAttendee':
				$provider = 'tribe-commerce';
				break;
			case 'tec_tc_ticket':
			case 'TcTicket':
			case 'tec_tc_attendee':
			case 'TcAttendee':
			case 'tec_tc_order':
			case 'TcOrder':
				$provider = 'tickets-commerce';
				break;
			default:
				break;
		}

		return $provider;
	}

	/**
	 * Helper function to check if the post type is a TEC type.
	 *
	 * @param string $post_type the post type name.
	 */
	public static function is_tec_post_type( string $post_type ) : bool {
		$registered_post_types = self::get_registered_post_types();

		return in_array( $post_type, array_keys( $registered_post_types ), true );
	}

	/**
	 * Gets an array of post types that can have tickets.
	 */
	public static function get_enabled_post_types_for_tickets() : array {
		// Remove Event post type if its not registered.
		$types = tribe_get_option( 'ticket-enabled-post-types' ) ?? [];

		if ( ! TEC::is_tec_loaded() ) {
			$types = array_diff( $types, [ 'tribe_events' ] );
		}

		return $types;
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

	/**
	 * Converts a sentence to camelcase
	 *
	 * @param string $str .
	 * @param array  $no_strip additional characters to keep.
	 */
	public static function to_camel_case( string $str, array $no_strip = [] ) : string {
		// non-alpha and non-numeric characters become spaces.
		$str = preg_replace( '/[^a-z0-9' . implode( '', $no_strip ) . ']+/i', ' ', $str ) ?? '';
		$str = trim( $str );
		// uppercase the first character of each word.
		$str = ucwords( $str );
		$str = str_replace( ' ', '', $str );
		$str = lcfirst( $str );

		return $str;
	}
}
