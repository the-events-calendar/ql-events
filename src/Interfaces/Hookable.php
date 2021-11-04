<?php
/**
 * Interface for classes containing WordPress action/filter hooks.
 *
 * @package \WPGraphQL\TEC
 * @since 0.0.1
 */

namespace WPGraphQL\TEC\Interfaces;

/**
 * Interface - Hookable
 */
interface Hookable {
	/**
	 * Register hooks with WordPress.
	 */
	public static function register_hooks() : void;
}
