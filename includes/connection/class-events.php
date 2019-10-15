<?php
/**
 * Connection - Events
 *
 * Registers connections to Events
 *
 * @package WPGraphQL\Extensions\QL_Events\Connection
 * @since   0.0.1
 */

namespace WPGraphQL\Extensions\QL_Events\Connection;

use WPGraphQL\Types;
use WPGraphQL\TypeRegistry;

/**
 * Class - Events
 */
class Events {
	/**
	 * Filters
	 */
	public static function where_args() {
		return array(
			'venuesIn'         => array(
				'type'        => Types::list_of( TypeRegistry::get_type( 'Int' ) ),
				'description' => __( 'Filter the connection based on event venue ID', 'ql-events' ),
			),
			'venuesNotIn'         => array(
				'type'        => Types::list_of( TypeRegistry::get_type( 'Int' ) ),
				'description' => __( 'Filter the connection based on event venue ID', 'ql-events' ),
			),
			'startDateQuery' => array(
				'type'        => TypeRegistry::get_type( 'DateQueryInput' ),
				'description' => __( 'Filter the connection based on event start dates', 'ql-events' ),
			),
			'endDateQuery'   => array(
				'type'        => TypeRegistry::get_type( 'DateQueryInput' ),
				'description' => __( 'Filter the connection based on event end dates', 'ql-events' ),
			),
		);
	}
}
