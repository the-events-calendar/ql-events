<?php
/**
 * QL_Events
 *
 * @package WPGraphQL\QL_Events
 * @since 0.0.1
 */

namespace WPGraphQL\QL_Events;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( '\WPGraphQL\QL_Events\QL_Events' ) ) :
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
		 * QL_Events constructor
		 */
		private function __construct() {
			add_action( 'graphql_init', [ $this, 'init' ] );
			add_action( 'plugins_loaded', [ $this, 'render_admin_notices' ] );
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
			_doing_it_wrong( __FUNCTION__, esc_html__( 'QL_Events class should not be cloned.', 'ql-events' ), esc_html( QL_EVENTS_VERSION ) );
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
			_doing_it_wrong( __FUNCTION__, esc_html__( 'De-serializing instances of the QL_Events class is not allowed', 'ql-events' ), esc_html( QL_EVENTS_VERSION ) );
		}

		/**
		 * QL_Events Constructor
		 *
		 * @since 0.0.1
		 *
		 * @return QL_Events
		 */
		public static function instance() {
			if ( ! isset( self::$instance ) && ! ( is_a( self::$instance, __CLASS__ ) ) ) {
				if ( ! function_exists( 'is_plugin_active' ) ) {
					require_once ABSPATH . 'wp-admin/includes/plugin.php';
				}
				self::$instance = new self();
			}

			/**
			 * Return the QL_Events Instance
			 */
			return self::$instance;
		}

		/**
		 * Checks if QL Events required plugins are installed and activated
		 *
		 * @since 0.3.0
		 *
		 * @return array
		 */
		public function dependencies_not_ready() {
			if ( ! class_exists( 'WPGraphQL' ) ) {
				$deps[] = [ 'https://wpgraphql.com', 'WPGraphQL' ];
			}
			if ( ! class_exists( 'Tribe__Events__Main' ) ) {
				$deps[] = [ 'https://theeventscalendar.com', 'The Events Calendar' ];
			}
			$deps = [];

			// Don't check WPGraphQL settings if WPGraphQL is not installed.
			if ( ! class_exists( '\WPGraphQL' ) ) {
				return $deps;
			}

			if ( self::is_events_pro_support_enabled() && ! self::is_events_pro_active() ) {
				$deps[] = [
					'https://woocommerce.com/products/composite-products',
					'The Events Calendar Pro',
				];
			}

			if ( self::is_event_tickets_support_enabled() && ! self::is_event_tickets_active() ) {
				$deps[] = [
					'https://woocommerce.com/products/product-add-ons',
					'Event Tickets',
				];
			}

			if ( self::is_event_tickets_plus_support_enabled() && ! self::is_event_tickets_plus_active() ) {
				$deps[] = [
					'https://woocommerce.com/products/product-bundles',
					'Event Tickets Plus',
				];
			}

			if ( ! defined( 'QL_EVENTS_TEST_MODE' ) && self::is_event_tickets_plus_support_enabled() && ! self::is_woographql_active() ) {
				$deps[] = [
					'https://woographql.com',
					'WooGraphQL',
				];
			}

			if ( self::is_events_virtual_support_enabled() && ! self::is_events_virtual_support_enabled() ) {
				$deps[] = [
					'https://woocommerce.com/products/woocommerce-subscriptions',
					'Events Virtual',
				];
			}

			return $deps;
		}

		/**
		 * Returns true if the "QL_EVENTS_TEST_MODE" set to a "truthy" value.
		 *
		 * @since 0.3.0
		 *
		 * @return boolean
		 */
		public static function is_test_mode_active() {
			return defined( 'QL_EVENTS_TEST_MODE' ) && QL_EVENTS_TEST_MODE;
		}

		/**
		 * Returns true if the "enable_events_pro_support" is "on"
		 *
		 * @since 0.3.0
		 *
		 * @return boolean
		 */
		public static function is_events_pro_support_enabled() {
			return 'on' === ql_events_setting( 'enable_events_pro_support', 'off' )
				|| self::is_test_mode_active();
		}

		/**
		 * Returns if Virtual Events is installed and activated.
		 *
		 * @since 0.3.0
		 *
		 * @return bool
		 */
		public static function is_events_pro_active() {
			return class_exists( 'Tribe__Events__Pro__Main' );
		}

		/**
		 * Returns true if the "enable_event_tickets_support" is "on"
		 *
		 * @since 0.3.0
		 *
		 * @return boolean
		 */
		public static function is_event_tickets_support_enabled() {
			return 'on' === ql_events_setting( 'enable_event_tickets_support', 'off' )
				|| self::is_test_mode_active();
		}

		/**
		 * Returns if Event Tickets is installed and activated
		 *
		 * @since 0.3.0
		 *
		 * @return bool
		 */
		public static function is_event_tickets_active() {
			if ( ! class_exists( '\Tribe__Tickets__Main' ) ) {
				return false;
			}
			if ( ! tribe_isset_var( 'tickets.rsvp' ) ) {
				return false;
			}
			if ( ! tribe_isset_var( 'tickets.commerce.paypal' ) ) {
				return false;
			}
			return true;
		}

		/**
		 * Returns true if the "enable_event_tickets_plus_support" is "on"
		 *
		 * @since 0.3.0
		 *
		 * @return boolean
		 */
		public static function is_event_tickets_plus_support_enabled() {
			return 'on' === ql_events_setting( 'enable_event_tickets_plus_support', 'off' )
				|| self::is_test_mode_active();
		}

		/**
		 * Returns if Ticket Events Plus is installed and activated
		 *
		 * @since 0.3.0
		 *
		 * @return bool
		 */
		public static function is_event_tickets_plus_active() {
			$activated = function_exists( 'tribe_check_plugin' );
			if ( $activated ) {
				$tickets_plus_can_run = self::is_event_tickets_active()
					&& class_exists( 'Tribe__Tickets_Plus__Main' )
					&& tribe_check_plugin( 'Tribe__Tickets_Plus__Main' );

				return apply_filters( 'tribe_event_tickets_plus_can_run', $tickets_plus_can_run ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
			}

			return false;
		}

		/**
		 * Returns if WooGraphQL is installed and activate
		 *
		 * @since 0.3.0
		 *
		 * @return bool
		 */
		public static function is_woographql_active() {
			return defined( 'WPGRAPHQL_WOOCOMMERCE_VERSION' );
		}

		/**
		 * Returns true if the "enable_events_virtual_support" is "on"
		 *
		 * @since 0.3.0
		 *
		 * @return boolean
		 */
		public static function is_events_virtual_support_enabled() {
			return 'on' === ql_events_setting( 'enable_events_virtual_support', 'off' )
				|| self::is_test_mode_active();
		}

		/**
		 * Returns if Virtual Events is installed and activated.
		 *
		 * @since 0.3.0
		 *
		 * @return bool
		 */
		public static function is_events_virtual_active() {
			return class_exists( 'Tribe\Events\Virtual\Plugin' );
		}

		/**
		 * Render admin notices for all missing dependencies.
		 *
		 * @since 0.3.0
		 *
		 * @return void
		 */
		public function render_admin_notices() {
			$not_ready = $this->dependencies_not_ready();

			foreach ( $not_ready as $dep ) {
				add_missing_dependencies_notice( $dep );
			}
		}

		/**
		 * Initializes QL Events.
		 *
		 * @since 0.0.1
		 *
		 * @return void
		 */
		public function init() {
			// Load include files.
			$this->includes();

			// Initialize QL Events.
			new Admin();

			// Bail early, if some dependencies still needed.
			if ( ! empty( $this->dependencies_not_ready() ) ) {
				return;
			}

			// Setup schema.
			$this->setup();

			/**
			 * Fire off init action
			 *
			 * @param QL_Events $this The instance of the QL_Events class
			 */
			do_action( 'ql_events_init', $this );
		}

		/**
		 * Returns The Events Calendar and core extensions post-types registered to the schema.
		 *
		 * @since 0.3.0
		 *
		 * @return array
		 */
		public static function get_post_types() {
			return apply_filters(
				'ql_events_register_post_types',
				[
					'tribe_events',
					'tribe_venue',
					'tribe_organizer',
				]
			);
		}

		/**
		 * Returns The Events Calendar and core extensions taxonomies registered to the schema.
		 *
		 * @since 0.3.0
		 *
		 * @return array
		 */
		public static function get_taxonomies() {
			return apply_filters(
				'ql_events_register_taxonomies',
				[
					'tribe_events_cat',
				]
			);
		}

		/**
		 * Include plugin files.
		 *
		 * @access private
		 * @since  0.0.1
		 *
		 * @return void
		 */
		private function includes() {
			$include_directory_path = get_includes_directory();

			require $include_directory_path . 'admin/class-section.php';
			require $include_directory_path . 'admin/class-general.php';

			require $include_directory_path . 'types/interface/class-ticket-field.php';
			require $include_directory_path . 'types/interface/class-attendee-interface.php';
			require $include_directory_path . 'types/interface/class-order-interface.php';
			require $include_directory_path . 'types/interface/class-ticket-interface.php';
			require $include_directory_path . 'types/enum/class-events-virtual-show-embed-at-enum.php';
			require $include_directory_path . 'types/enum/class-events-virtual-show-embed-to-enum.php';
			require $include_directory_path . 'types/input/class-meta-data-input.php';
			require $include_directory_path . 'types/object/common/trait-attendee.php';
			require $include_directory_path . 'types/object/common/trait-order.php';
			require $include_directory_path . 'types/object/common/trait-ticket.php';
			require $include_directory_path . 'types/object/ticket-field/class-birthdate.php';
			require $include_directory_path . 'types/object/ticket-field/class-checkbox.php';
			require $include_directory_path . 'types/object/ticket-field/class-date.php';
			require $include_directory_path . 'types/object/ticket-field/class-dropdown.php';
			require $include_directory_path . 'types/object/ticket-field/class-email.php';
			require $include_directory_path . 'types/object/ticket-field/class-phone.php';
			require $include_directory_path . 'types/object/ticket-field/class-radio.php';
			require $include_directory_path . 'types/object/ticket-field/class-text.php';
			require $include_directory_path . 'types/object/ticket-field/class-url.php';
			require $include_directory_path . 'types/object/class-event-linked-data-type.php';
			require $include_directory_path . 'types/object/class-event-type.php';
			require $include_directory_path . 'types/object/class-organizer-linked-data-type.php';
			require $include_directory_path . 'types/object/class-organizer-type.php';
			require $include_directory_path . 'types/object/class-paypalattendee-type.php';
			require $include_directory_path . 'types/object/class-paypalorder-type.php';
			require $include_directory_path . 'types/object/class-paypalticket-type.php';
			require $include_directory_path . 'types/object/class-rsvpattendee-type.php';
			require $include_directory_path . 'types/object/class-rsvpticket-type.php';
			require $include_directory_path . 'types/object/class-ticket-linked-data-type.php';
			require $include_directory_path . 'types/object/class-venue-linked-data-type.php';
			require $include_directory_path . 'types/object/class-venue-type.php';
			require $include_directory_path . 'types/object/class-wooattendee-type.php';
			require $include_directory_path . 'types/object/class-wooticket-type.php';
			require $include_directory_path . 'types/object/class-wooorder-type.php';
			require $include_directory_path . 'types/object/class-meta-data-type.php';

			require $include_directory_path . 'data/connection/class-attendee-connection-resolver.php';
			require $include_directory_path . 'data/connection/class-event-connection-resolver.php';
			require $include_directory_path . 'data/connection/class-organizer-connection-resolver.php';
			require $include_directory_path . 'data/connection/class-ticket-connection-resolver.php';
			require $include_directory_path . 'data/class-factory.php';

			require $include_directory_path . 'mutation/class-register-attendee.php';
			require $include_directory_path . 'mutation/class-update-attendee.php';

			require $include_directory_path . 'connection/class-attendees.php';
			require $include_directory_path . 'connection/class-events.php';
			require $include_directory_path . 'connection/class-organizers.php';
			require $include_directory_path . 'connection/class-tickets.php';
			require $include_directory_path . 'connection/class-tickets-plus.php';

			require $include_directory_path . 'class-admin.php';
			require $include_directory_path . 'class-core-schema-filters.php';
			require $include_directory_path . 'class-tickets-filters.php';
			require $include_directory_path . 'class-tickets-plus-filters.php';
			require $include_directory_path . 'class-type-registry.php';
		}

		/**
		 * Sets up QL Events schema.
		 *
		 * @since 0.0.1
		 * @access private
		 *
		 * @return void
		 */
		private function setup() {
			// WPGraphQL core filters.
			Core_Schema_Filters::add_filters();

			if ( self::is_event_tickets_support_enabled() && self::is_event_tickets_active() ) {
				Tickets_Filters::add_filters();
			}

			if ( self::is_event_tickets_plus_support_enabled() && self::is_event_tickets_plus_active()
				&& self::is_woographql_active() ) {
				Tickets_Plus_Filters::add_filters();
			}

			// Initialize QL Events type registry.
			$registry = new Type_Registry();
			add_action( 'graphql_register_types', [ $registry, 'init' ], 10 );
		}
	}
endif;
