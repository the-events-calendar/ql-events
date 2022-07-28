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
			require get_includes_directory() . 'types/interface/class-ticket-interface.php';
			require get_includes_directory() . 'types/object/common/trait-attendee.php';
			require get_includes_directory() . 'types/object/common/trait-order.php';
			require get_includes_directory() . 'types/object/common/trait-ticket.php';
			require get_includes_directory() . 'types/object/class-event-linked-data-type.php';
			require get_includes_directory() . 'types/object/class-event-type.php';
			require get_includes_directory() . 'types/object/class-organizer-linked-data-type.php';
			require get_includes_directory() . 'types/object/class-organizer-type.php';
			require get_includes_directory() . 'types/object/class-paypalattendee-type.php';
			require get_includes_directory() . 'types/object/class-paypalorder-type.php';
			require get_includes_directory() . 'types/object/class-paypalticket-type.php';
			require get_includes_directory() . 'types/object/class-rsvpattendee-type.php';
			require get_includes_directory() . 'types/object/class-rsvpticket-type.php';
			require get_includes_directory() . 'types/object/class-ticket-linked-data-type.php';
			require get_includes_directory() . 'types/object/class-venue-linked-data-type.php';
			require get_includes_directory() . 'types/object/class-venue-type.php';
			require get_includes_directory() . 'types/object/class-wooattendee-type.php';

			require get_includes_directory() . 'data/connection/class-attendee-connection-resolver.php';
			require get_includes_directory() . 'data/connection/class-event-connection-resolver.php';
			require get_includes_directory() . 'data/connection/class-organizer-connection-resolver.php';
			require get_includes_directory() . 'data/connection/class-ticket-connection-resolver.php';
			require get_includes_directory() . 'data/class-factory.php';

			require get_includes_directory() . 'connection/class-attendees.php';
			require get_includes_directory() . 'connection/class-events.php';
			require get_includes_directory() . 'connection/class-organizers.php';
			require get_includes_directory() . 'connection/class-tickets.php';

			require get_includes_directory() . 'class-core-schema-filters.php';
			require get_includes_directory() . 'class-type-registry.php';
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
