<?php
/**
 * Connection - Events
 *
 * Registers connections to Events
 *
 * @package WPGraphQL\QL_Events\Connection
 * @since   0.0.1
 */

namespace WPGraphQL\QL_Events\Connection;

use WPGraphQL\Types;

/**
 * Class - Events
 */
class Events {
	/**
	 * Returns Venue connection where arguments.
	 *
	 * @since 0.0.1
	 *
	 * @return array
	 */
	public static function where_args() {
		return [
			'venuesIn'       => [
				'type'        => [ 'list_of' => 'Int' ],
				'description' => __( 'Filter the connection based on event venue ID', 'ql-events' ),
			],
			'venuesNotIn'    => [
				'type'        => [ 'list_of' => 'Int' ],
				'description' => __( 'Filter the connection based on event venue ID', 'ql-events' ),
			],
			'startDateQuery' => [
				'type'        => 'DateQueryInput',
				'description' => __( 'Filter the connection based on event start dates', 'ql-events' ),
			],
			'endDateQuery'   => [
				'type'        => 'DateQueryInput',
				'description' => __( 'Filter the connection based on event end dates', 'ql-events' ),
			],
		];
	}
}
