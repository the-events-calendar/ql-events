<?php
/**
 * This file contains access functions for various class methods
 *
 * @package WPGraphQL\QL_Events
 * @since 0.1.0
 */

if ( ! function_exists( 'ql_events_setting' ) ) :
	/**
	 * Get an option value from QL Events settings
	 *
	 * @param string $option_name  The key of the option to return.
	 * @param mixed  $default      The default value the setting should return if no value is set.
	 * @param string $section_name The settings section name.
	 *
	 * @return mixed|string|int|boolean
	 */
	function ql_events_setting( string $option_name, $default = '', $section_name = 'ql_events_settings' ) {
		$section_fields = get_option( $section_name );

		/**
		 * Filter the section fields
		 *
		 * @param array  $section_fields The values of the fields stored for the section
		 * @param string $section_name   The name of the section
		 * @param mixed  $default        The default value for the option being retrieved
		 */
		$section_fields = apply_filters( 'ql_events_settings_section_fields', $section_fields, $section_name, $default );

		/**
		 * Get the value from the stored data, or return the default
		 */
		$value = isset( $section_fields[ $option_name ] ) ? $section_fields[ $option_name ] : $default;

		/**
		 * Filter the value before returning it
		 *
		 * @param mixed  $value          The value of the field
		 * @param mixed  $default        The default value if there is no value set
		 * @param string $option_name    The name of the option
		 * @param array  $section_fields The setting values within the section
		 * @param string $section_name   The name of the section the setting belongs to
		 */
		return apply_filters( 'ql_events_settings_section_field_value', $value, $default, $option_name, $section_fields, $section_name );
	}
endif;

if ( ! function_exists( 'ql_events_ends_with' ) ) :
	/**
	 * Simple "endsWith" function because PHP still doesn't have on built-in.
	 *
	 * @param string $haystack  Source string.
	 * @param string $needle    Target substring.
	 *
	 * @return bool
	 */
	function ql_events_ends_with( $haystack, $needle ) {
		$length = strlen( $needle );
		if ( 0 === $length ) {
			return true;
		}

		return ( substr( $haystack, -$length ) === $needle );
	}
endif;
