<?php
/**
 * QL_Events
 *
 * @package WPGraphQL\QL_Events
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
		 * Stores the instance of the WPGraphQL\QL_Events class
		 *
		 * @var QL_Events The one true WPGraphQL\QL_Events
		 * @access private
		 */
		private static $instance;

		/**
		 * QL_Events Constructor
		 */
		public static function instance() {
			if ( ! isset( self::$instance ) && ! ( is_a( self::$instance, __CLASS__ ) ) ) {
				if ( ! function_exists( 'is_plugin_active' ) ) {
					require_once ABSPATH . 'wp-admin/includes/plugin.php';
				}
				self::$instance = new self();
				self::$instance->constants();
				self::$instance->includes();
				self::$instance->setup();
			}

			/**
			 * Fire off init action
			 *
			 * @param QL_Events $instance The instance of the QL_Events class
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
		 * Returns if Ticket Events Plus is installed and activate
		 *
		 * @return bool
		 */
		public static function is_ticket_events_loaded() {
			if ( ! class_exists( '\Tribe__Tickets__Main' ) ) {
				return false;
			}
			if ( ! tribe_isset_var ( 'tickets.rsvp' ) ) {
				return false;
			}
			if ( ! tribe_isset_var ( 'tickets.commerce.paypal' ) ) {
				return false;
			}
			return true;
		}

		/**
		 * Returns if Ticket Events Plus is installed and activate
		 *
		 * @return bool
		 */
		public static function is_ticket_events_plus_loaded() {
			$activated = function_exists( 'tribe_check_plugin' );
			if ( $activated ) {
				$tickets_plus_can_run = self::is_ticket_events_loaded()
					&& class_exists( 'Tribe__Tickets_Plus__Main' )
					&& tribe_check_plugin( 'Tribe__Tickets_Plus__Main' );

				return apply_filters( 'tribe_event_tickets_plus_can_run', $tickets_plus_can_run );
			}

			return false;
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
		 * Sets up QL Events schema.
		 */
		private function setup() {
			// WPGraphQL core filters.
			\WPGraphQL\QL_Events\Core_Schema_Filters::add_filters();

			// Initialize QL Events type registry.
			$registry = new \WPGraphQL\QL_Events\Type_Registry();
			add_action( 'graphql_register_types', array( $registry, 'init' ), 10, 1 );
		}
	}
endif;
