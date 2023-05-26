<?php
/**
 * The section defines the root functionality for a settings section
 *
 * @package WPGraphQL\WooCommerce\Admin
 * @since TBD
 */

namespace WPGraphQL\QL_Events\Admin;

/**
 * Section class
 */
abstract class Section {

	/**
	 * Returns Section settings fields.
	 *
	 * @since TBD
	 *
	 * @return array
	 */
	abstract public static function get_fields();
}
