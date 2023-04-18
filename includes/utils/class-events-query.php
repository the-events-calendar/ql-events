<?php
/**
 * WP_Query clone for wrapping the Tribe__Events__Query::getEvents() function
 *
 * @package WPGraphQL\QL_Events\Utils
 */
namespace WPGraphQL\QL_Events\Utils;

use Tribe__Events__Query as Query;

/**
 * class Events_Query
 */
class Events_Query {
	/**
	 * Stores query results.
	 *
	 * @var \WP_Query
	 */
	private $query;

	/**
	 * Events_Query constructor.
	 */
	public function __construct( $args = [] ) {
		$this->query = Query::getEvents( $args, true );
	}

	/**
	 * Pass thru for query instance.
	 *
	 * @param string $name
	 * @return mixed
	 */
	public function __get( $name ) {
		return $this->query->$name;
	}
}
