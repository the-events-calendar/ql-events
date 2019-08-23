<?php
/**
 * QL_Events
 *
 * @package WPGraphQL\Extensions\QL_Events
 * @since 0.0.1
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'QL_Events' ) ) :
	/**
	 * Class QL_Events
	 */
	final class QL_Events {

		/**
		 * Stores the instance of the WPGraphQL\Extensions\QL_Events class
		 *
		 * @var QL_Events The one true WPGraphQL\Extensions\QL_Events
		 * @access private
		 */
		private static $instance;

		/**
		 * WP_GraphQL_WooCommerce Constructor
		 */
		public static function instance() {
			if ( ! isset( self::$instance ) && ! ( is_a( self::$instance, __CLASS__ ) ) ) {
				self::$instance = new self();
				self::$instance->constants();
				self::$instance->includes();
				self::$instance->actions();
				self::$instance->filters();
			}

			/**
			 * Fire off init action
			 *
			 * @param WPGraphQLWooCommerce $instance The instance of the QL_Events class
			 */
			do_action( 'ql_events_init', self::$instance );

			/**
			 * Return the QL_Events Instance
			 */
			return self::$instance;
		}

		/**
		 * Returns The Events Calendar and core extensions post-types registered to the schema.
		 *
		 * @return array
		 */
		public static function get_post_types() {
			return apply_filters(
				'register_ql_events_post_types',
				array(
					'tribe_events',
					'tribe_venue',
					'tribe_organizer',
				)
			);
		}

		/**
		 * Returns The Events Calendar and core extensions taxonomies registered to the schema.
		 *
		 * @return array
		 */
		public static function get_taxonomies() {
			return apply_filters(
				'register_ql_events_taxonomies',
				array(
					'tribe_events_cat',
				)
			);
		}

		/**
		 * Throw error on object clone.
		 * The whole idea of the singleton design pattern is that there is a single object
		 * therefore, we don't want the object to be cloned.
		 *
		 * @since  0.0.1
		 * @access public
		 * @return void
		 */
		public function __clone() {
			// Cloning instances of the class is forbidden.
			_doing_it_wrong( __FUNCTION__, esc_html__( 'QL_Events class should not be cloned.', 'ql-events' ), '0.0.1' );
		}

		/**
		 * Disable unserializing of the class.
		 *
		 * @since  0.0.1
		 * @access protected
		 * @return void
		 */
		public function __wakeup() {
			// De-serializing instances of the class is forbidden.
			_doing_it_wrong( __FUNCTION__, esc_html__( 'De-serializing instances of the QL_Events class is not allowed', 'ql-events' ), '0.0.1' );
		}

		/**
		 * Defines constants used throughout the schema.
		 *
		 * @access private
		 * @since 0.0.1
		 * @return void
		 */
		private function constants() {
			define( 'TEC_EVENT_TICKETS_LOADED', class_exists( '\Tribe__Tickets__Main' ) );
			define( 'TEC_EVENT_TICKETS_PLUS_LOADED', class_exists( '\Tribe__Tickets_Plus__Main' ) );
		}

		/**
		 * Include required files.
		 * Uses composer's autoload
		 *
		 * @access private
		 * @since  0.0.1
		 * @return void
		 */
		private function includes() {
			/**
			 * Autoload Required Classes
			 */
			if ( defined( 'QL_EVENTS_AUTOLOAD' ) && false !== QL_EVENTS_AUTOLOAD ) {
				require_once QL_EVENTS_PLUGIN_DIR . 'vendor/autoload.php';
			}

			// Required non-autoloaded classes.
			require_once QL_EVENTS_PLUGIN_DIR . 'class-inflect.php';
		}

		/**
		 * Sets up actions to run at certain spots throughout WordPress and the WPGraphQL execution cycle
		 */
		private function actions() {
			/**
			 * Setup actions
			 */
			\WPGraphQL\Extensions\QL_Events\Type_Registry::add_actions();
		}

		/**
		 * Sets up filters to run at certain spots throughout WordPress and the WPGraphQL execution cycle
		 */
		private function filters() {
			/**
			 * Setup filters
			 */
			\WPGraphQL\Extensions\QL_Events\Core_Schema_Filters::add_filters();
		}
	}
endif;
