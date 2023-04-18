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
		 * Returns if WooGraphQL is installed and activate
		 *
		 * @return bool
		 */
		public static function is_woographql_loaded() {
			return class_exists( 'WPGraphQL\WooCommerce\WP_GraphQL_WooCommerce' );
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
			if ( ! tribe_isset_var( 'tickets.rsvp' ) ) {
				return false;
			}
			if ( ! tribe_isset_var( 'tickets.commerce.paypal' ) ) {
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

				return apply_filters( 'tribe_event_tickets_plus_can_run', $tickets_plus_can_run ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
			}

			return false;
		}

		/**
		 * Returns if Virtual Events is installed and activated.
		 *
		 * @return bool
		 */
		public static function is_events_pro_loaded() {
			return class_exists( 'Tribe__Events__Pro__Main' );
		}

		/**
		 * Returns if Virtual Events is installed and activated.
		 *
		 * @return bool
		 */
		public static function is_virtual_events_loaded() {
			return class_exists( 'Tribe\Events\Virtual\Plugin' );
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
		 * Include plugin files.
		 *
		 * @access private
		 * @since  0.0.1
		 * @return void
		 */
		private function includes() {
			$include_directory_path = get_includes_directory();
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

			require $include_directory_path . 'class-core-schema-filters.php';
			require $include_directory_path . 'class-type-registry.php';
		}

		/**
		 * Sets up QL Events schema.
		 */
		private function setup() {
			// WPGraphQL core filters.
			\WPGraphQL\QL_Events\Core_Schema_Filters::add_filters();

			// Initialize QL Events type registry.
			$registry = new \WPGraphQL\QL_Events\Type_Registry();
			add_action( 'graphql_register_types', [ $registry, 'init' ], 10, 1 );
		}
	}
endif;
