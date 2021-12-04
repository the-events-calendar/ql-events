<?php
/**
 * Initializes a singleton instance of WP_GraphQL_WooCommerce
 *
 * @package WPGraphQL\TEC
 * @since 0.0.1
 */

namespace WPGraphQL\TEC;

use WPGraphQL\TEC\Events\CoreSchemaFilters as EventsSchemaFilters;
use WPGraphQL\TEC\EventsPro\CoreSchemaFilters as EventsProSchemaFilters;
use WPGraphQL\TEC\Tickets\CoreSchemaFilters as TicketsSchemaFilters;

if ( ! class_exists( 'WPGraphQL\TEC\TEC' ) ) :

	/**
	 * Class TEC
	 */
	final class TEC {
		/**
		 * Stores the instance of the WPGraphQL\TEC class
		 *
		 * @var TEC The one true WPGraphQL\TEC
		 * @access private
		 */
		private static $instance;

		/**
		 * TEC Constructor
		 */
		public static function instance() : self {
			if ( ! isset( self::$instance ) && ! ( is_a( self::$instance, __CLASS__ ) ) ) {
				if ( ! function_exists( 'is_plugin_active' ) ) {
					require_once ABSPATH . 'wp-admin/includes/plugin.php';
				}
				self::$instance = new self();
				self::$instance->includes();
				self::$instance->setup();
			}

			/**
			 * Fire off init action
			 *
			 * @param TEC $instance The instance of the TEC class
			 */
			do_action( 'graphql_tec_init', self::$instance );

			/**
			 * Return the TEC Instance
			 */
			return self::$instance;
		}

		/**
		 * Returns true if The Events Calendar is activated.
		 */
		public static function is_tec_loaded() : bool {
			return class_exists( 'Tribe__Events__Main' );
		}

		/**
		 * Returns true if The Events Calendar Pro is activated.
		 */
		public static function is_ecp_loaded() : bool {
			$activated = function_exists( 'tribe_check_plugin' );
			if ( $activated ) {
				$tickets_plus_can_run = self::is_tec_loaded()
					&& class_exists( 'Tribe__Events__Pro__Main' )
					&& tribe_check_plugin( 'Tribe__Events__Pro__Main' );

				return apply_filters( 'tribe_events_calendar_pro_can_run', $tickets_plus_can_run );
			}

			return false;
		}

		/**
		 * Returns true if Event Tickets is activated.
		 */
		public static function is_et_loaded() : bool {
			return class_exists( 'Tribe__Tickets__Main' );
		}

		/**
		 * Returns true if Event Tickets Plus is activated.
		 */
		public static function is_etp_loaded() : bool {
			$activated = function_exists( 'tribe_check_plugin' );
			if ( $activated ) {
				$tickets_plus_can_run = self::is_et_loaded()
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
			_doing_it_wrong( __FUNCTION__, esc_html__( 'QL_Events class should not be cloned.', 'wp-graphql-tec' ), '0.0.1' );
		}

		/**
		 * Disable unserializing of the class.
		 *
		 * @since  0.0.1
		 * @access protected
		 * @return void
		 */
		public function __wakeup() : void {
			// De-serializing instances of the class is forbidden.
			_doing_it_wrong( __FUNCTION__, esc_html__( 'De-serializing instances of the QL_Events class is not allowed', 'wp-graphql-tec' ), '0.0.1' );
		}

		/**
		 * Include required files.
		 * Uses composer's autoload
		 *
		 * @access private
		 * @since  0.0.1
		 */
		private function includes() : void {
			/**
			 * Autoload Required Classes
			 */
			if ( defined( 'WPGRAPHQL_TEC_AUTOLOAD' ) && false !== WPGRAPHQL_TEC_AUTOLOAD && defined( 'WPGRAPHQL_TEC_PLUGIN_DIR' ) ) {
				require_once WPGRAPHQL_TEC_PLUGIN_DIR . 'vendor/autoload.php';
			}
		}

		/**
		 * Sets up TEC schema.
		 */
		private function setup() : void {
			// WPGraphQL core filters.
			if ( self::is_tec_loaded() ) {
				EventsSchemaFilters::register_hooks();
			}

			if ( self::is_ecp_loaded() ) {
				EventsProSchemaFilters::register_hooks();
			}

			if ( self::is_et_loaded() ) {
				TicketsSchemaFilters::register_hooks();
			}

			// Initialize TEC type registry.
			add_action( get_graphql_register_action(), [ TypeRegistry::class, 'init' ] );
		}
	}

endif;
