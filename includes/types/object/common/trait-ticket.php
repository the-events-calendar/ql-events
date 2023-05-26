<?php
/**
 * Defines common fields for Ticket Events' Ticket types
 *
 * @package \WPGraphQL\QL_Events\Type\WPObject
 * @since   0.0.1
 */

namespace WPGraphQL\QL_Events\Type\WPObject;

use WPGraphQL\AppContext;
use WPGraphQL\Data\DataSource;

/**
 * Trait - Ticket
 */
trait Ticket {
	/**
	 * Define common Ticket fields
	 *
	 * @since 0.0.1
	 *
	 * @return array
	 */
	public static function fields() {
		return [];
	}
}
