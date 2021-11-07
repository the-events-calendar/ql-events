<?php
/**
 * GraphQL Object Type - TecSettings
 *
 * @package WPGraphQL\TEC\Type\Object
 * @since 0.0.1
 */

namespace WPGraphQL\TEC\Type\WPObject;

use Tribe__Date_Utils;
use Tribe__Settings_Manager;
use WPGraphQL\TEC\Type\Enum\EnabledViewsEnum;
use WPGraphQL\TEC\Type\Enum\EventsTemplateEnum;
use WPGraphQL\TEC\Type\Enum\TimezoneModeEnum;
use WPGraphQL\TEC\TEC;
/**
 * Class - TecSettings
 */
class TecSettings {
	/**
	 * Name of the type.
	 *
	 * @var string Type name.
	 */
	public static $type = 'TecSettings';

	/**
	 * {@inheritDoc}
	 */
	public static function register_type() : void {
		self::register_object_type();

		if ( TEC::is_tec_loaded() ) {
			self::register_tec_fields();
		}
		if ( TEC::is_et_loaded() ) {
			self::register_et_fields();
		}
	}

	/**
	 * Registers the object and core fields shared by TEC and ET.
	 */
	public static function register_object_type() : void {
		register_graphql_object_type(
			self::$type,
			[
				'description' => __( 'The Events Calendar site settings', 'wp-graphql-tec' ),
				'fields'      => [
					'datepickerFormat' => [
						'type'       => 'String',
						'descripton' => __( 'The date format used for elements with minimal space, such as in datepickers', 'wp-graphql-tec' ),
						'resolve'    => fn() => Tribe__Date_Utils::datepicker_formats( tribe_get_option( 'datepickerFormat' ) ),
					],
				],
			]
		);

		register_graphql_fields(
			'RootQuery',
			[
				'tecSettings' => [
					'type'        => self::$type,
					'description' => __( 'The Events Calendar site settings.', 'wp-graphql-tec' ),
					'resolve'     => function() {
						return Tribe__Settings_Manager::get_options();
					},
				],
			]
		);
	}

	/**
	 * Register the fields used by TEC Core plugin.
	 */
	public static function register_tec_fields() : void {
		register_graphql_fields(
			self::$type,
			[
				'afterHTML'                  => [
					'type'       => 'String',
					'descripton' => __( 'Additional code added after the event template.', 'wp-graphql-tec' ),
					'resolve'    => function () : ?string {
						ob_start();
						tribe_events_after_html();
						return ob_get_clean() ?: null;
					},
				],
				'beforeHTML'                 => [
					'type'       => 'String',
					'descripton' => __( 'Additional code added before the event template.', 'wp-graphql-tec' ),
					'resolve'    => function () : ?string {
						ob_start();
						tribe_events_before_html();
						return ob_get_clean() ?: null;
					},
				],
				'dateTimeSeparator'          => [
					'type'       => 'String',
					'descripton' => __( 'The separator that will be placed between the date and time, when both are shown.', 'wp-graphql-tec' ),
					'resolve'    => fn() => tribe_get_datetime_separator(),
				],
				'dateWithoutYearFormat'      => [
					'type'       => 'String',
					'descripton' => __( 'The format to use for displaying dates with the year. Used when displaying a date without a year. Used when showing an event from the current year.', 'wp-graphql-tec' ),
					'resolve'    => fn() => tribe_get_date_format( false ),
				],
				'dateWithYearFormat'         => [
					'type'       => 'String',
					'descripton' => __( 'The format to use for displaying dates with the year. Used when displaying a date in a future year.', 'wp-graphql-tec' ),
					'resolve'    => fn() => tribe_get_date_format( true ),
				],
				'defaultCurrencySymbol'      => [
					'type'        => 'String',
					'description' => __( 'Default currency symbol.', 'wp-graphql-tec' ),
				],
				'defaultView'                => [
					'type'       => EnabledViewsEnum::$type,
					'descripton' => __( 'The default event view.', 'wp-graphql-tec' ),
					'resolve'    => fn( $source ) => $source['viewOption'] ?: null,
				],
				'disableMetaboxCustomFields' => [
					'type'        => 'Boolean',
					'description' => __( 'Enable WordPress Custom Fields on events in the classic editor.', 'wp-graphql-tec' ),
					'resolve'     => fn( $source) => $source['disable_metabox_custom_fields'] ?? null,
				],
				'disableSearchBar'           => [
					'type'       => 'Boolean',
					'descripton' => __( 'Whether to use classic header instead.', 'wp-graphql-tec' ),
					'resolve'    => fn() => tribe_get_option( 'tribeDisableTribeBar', false ),
				],
				'eventsTemplate'             => [
					'type'       => EventsTemplateEnum::$type,
					'descripton' => __( 'The Events template.', 'wp-graphql-tec' ),
					'resolve'    => fn( $source ) => $source['tribeEventsTemplate'] ?: '',
				],
				'enabledViews'               => [
					'type'       => [ 'list_of' => EnabledViewsEnum::$type ],
					'descripton' => __( 'The event views enabled.', 'wp-graphql-tec' ),
					'resolve'    => fn( $source ) => $source['tribeEnableViews'] ?: null,
				],
				'embedGoogleMaps'            => [
					'type'        => 'Boolean',
					'description' => __( 'Whether to enable maps for events and venues.', 'wp-graphql-tec' ),
				],
				'embedGoogleMapsZoom'        => [
					'type'        => 'Integer',
					'description' => __( 'Google Maps default zoom level', 'wp-graphql-tec' ),
					'resolve'     => fn( $source ) => $source['embedGoogleMapsZoom'] ?: null,
				],
				'eventsSlug'                 => [
					'type'        => 'String',
					'description' => __( 'Events URL slug/', 'wp-graphql-tec' ),
				],
				// @todo Google Maps API Key
				'monthEventAmount'           => [
					'type'       => 'Integer',
					'descripton' => __( 'The number of events to display per day in month view. -1 for unlimited.', 'wp-graphql-tec' ),
				],
				'multiDayCutoff'             => [
					'type'        => 'String',
					'description' => __( 'End of day cutoff.', 'wp-graphql-tec' ),
				],
				'monthAndYearFormat'         => [
					'type'       => 'String',
					'descripton' => __( 'The format to use for dates that show a month and year only. Used on month view.', 'wp-graphql-tec' ),
					'resolve'    => fn() => tribe_get_date_option( 'monthAndYearFormat', 'F Y' ),
				],
				'postsPerPage'               => [
					'type'        => 'Int',
					'description' => __( 'The number of events per page on the List View. Does not affect other views.', 'wp-graphql-tec' ),
				],
				'reverseCurrencyPosition'    => [
					'type'        => 'Boolean',
					'description' => __( 'If the currency symbol shoud appear after the value.', 'wp-graphql-tec' ),
				],
				'showComments'               => [
					'type'        => 'Boolean',
					'description' => __( 'Enable comments on event pages.', 'wp-graphql-tec' ),
				],
				'showEventsInMainLoop'       => [
					'type'        => 'Boolean',
					'description' => __( 'Show events with the site\'s other posts. When this box is checked, events will also continue to appear on the default events page.', 'wp-graphql-tec' ),
				],
				'singleEventSlug'            => [
					'type'        => 'String',
					'description' => __( 'Single event URL slug.', 'wp-graphql-tec' ),
				],
				'timeRangeSeparator'         => [
					'type'       => 'String',
					'descripton' => __( 'The separator that will be used between the start and end time of an event.', 'wp-graphql-tec' ),
					'resolve'    => fn() => tribe_get_option( 'timeRangeSeparator', ' - ' ),
				],
				'timezoneMode'               => [
					'type'       => TimezoneModeEnum::$type,
					'descripton' => __( 'Time zone mode', 'wp-graphql-tec' ),
					'resolve'    => fn( $source ) => $source['tribe_events_timezone_mode'] ?? null,
				],
				'timezoneShowZone'           => [
					'type'       => 'Boolean',
					'descripton' => __( 'Whether to display the time zone to the end of the scheduling information.', 'wp-graphql-tec' ),
					'resolve'    => fn( $source ) => $source['tribe_events_timezones_show_zone'] ?? null,
				],
			]
		);
	}

	/**
	 * Register the fields used by ET plugin.
	 */
	public static function register_et_fields() : void {
		$fields                     = [];
		$is_tickets_commerce_loaded = tec_tickets_commerce_is_enabled();

		if ( TEC::is_tec_loaded() ) {
			$fields['rsvpFormLocation']            = [
				'type'        => 'TicketFormLocationOptionsEnum',
				'description' => __( 'The location of the RSVP ticket form. This setting only impacts events made with the classic editor. Defaults to `BEFORE_DETAILS`', 'wp-graphql-tec' ),
				'resolve'     => fn() =>  tribe_get_option( 'ticket-rsvp-form-location' ) ?: null,
			];
			$fields['commerceFormLocation']        = [
				'type'        => 'TicketFormLocationOptionsEnum',
				'description' => __( 'The location of the Commerce ticket form. This setting only impacts events made with the classic editor. Defaults to `BEFORE_DETAILS`', 'wp-graphql-tec' ),
				'resolve'     => fn() =>  tribe_get_option( 'ticket-commerce-form-location' ) ?: null,
			];
			$fields['displayTicketsLeftThreshold'] = [
				'type'        => 'Int',
				'description' => __( 'Will show a \"Number of Tickets Left\" message if the remaining number of tickets is below this number.', 'wp-graphql-tec' ),
				'resolve'     => fn() =>  tribe_get_option( 'ticket-display-tickets-left-threshold' ) ?: null,
			];
		}

		if ( $is_tickets_commerce_loaded ) {
			$fields['paypalEmail']                  = [
				'type'        => 'String',
				'description' => __( 'PayPal email to receive payments.', 'wp-graphql-tec' ),
				'resolve'     => fn() => trim( tribe_get_option( 'ticket-paypal-email' ) ) ?: null,
			];
			$fields['isPaypalIpnEnabled']           = [
				'type'        => 'Bool',
				'description' => __( 'Whether instant payment notifications (IPN) has been enabled in your PayPal account\'s Selling Tools.', 'wp-graphql-tec' ),
				'resolve'     => fn() => 'yes' === tribe_get_option( 'ticket-paypal-ipn-enabled' ),
			];
			$fields['isPaypalIpnAddressSet']        = [
				'type'        => 'Bool',
				'description' => __( 'Whether this site\'s address has been set in the Notification URL field in IPN Settings', 'wp-graphql-tec' ),
				'resolve'     => fn() => 'yes' === tribe_get_option( 'ticket-paypal-ipn-address-set' ),
			];
			$fields['isPaypalSandboxEnabled']       = [
				'type'        => 'Bool',
				'description' => __( 'Whether PayPal Sandbox mode for testing has been enabled.', 'wp-graphql-tec' ),
				'resolve'     => fn() => (bool) tribe_get_option( 'ticket-paypal-sandbox' ),
			];
			$fields['currencyCode']                 = [
				'type'        => 'PaypalCurrencyCodeOptionsEnum',
				'description' => __( 'Whether PayPal Sandbox mode for testing has been enabled.', 'wp-graphql-tec' ),
				'resolve'     => fn() => tribe_get_option( 'ticket-commerce-currency-code' ) ?: null,
			];
			$fields['stockHandling']                = [
				'type'        => 'StockHandlingOptionsEnum',
				'description' => __( 'Whether PayPal Sandbox mode for testing has been enabled.', 'wp-graphql-tec' ),
				'resolve'     => fn() => tribe_get_option( 'ticket-paypal-stock-handling' ) ?: 'on-pending',
			];
			$fields['successPageId']                = [
				'type'        => 'Int',
				'description' => __( 'The ID of the page to direct to after a successful PayPal order', 'wp-graphql-tec' ),
				'resolve'     => fn() => tribe_get_option( 'ticket-paypal-success-page' ) ?: null,
			];
			$fields['checkoutPageId']               = [
				'type'        => 'Int',
				'description' => __( 'The ID of the page where customers go to complete their purchase.', 'wp-graphql-tec' ),
				'resolve'     => fn() => tribe_get_option( 'tickets-commerce-checkout-page' ) ?: null,
			];
			$fields['confirmationEmailSenderEmail'] = [
				'type'        => 'String',
				'description' => __( 'Confirmation email sender address.', 'wp-graphql-tec' ),
				'resolve'     => fn() => tribe_get_option( 'ticket-paypal-confirmation-email-sender-email' ) ?: null,
			];
			$fields['confirmationEmailSenderName']  = [
				'type'        => 'String',
				'description' => __( 'Confirmation email sender name.', 'wp-graphql-tec' ),
				'resolve'     => fn() => tribe_get_option( 'ticket-paypal-confirmation-email-sender-name' ) ?: null,
			];
			$fields['confirmationEmailSubject']     = [
				'type'        => 'String',
				'description' => __( 'Confirmation email subject.', 'wp-graphql-tec' ),
				'resolve'     => fn() => tribe_get_option( 'ticket-paypal-confirmation-email-subject' ) ?: null,
			];
			if ( defined( 'WP_DEBUG' ) && true === WP_DEBUG ) {
				$fields['paypalNotifyUrl'] = [
					'type'        => 'String',
					'description' => __( 'A custom IPN notify url to override the default IPN. Only visible if `WP_DEBUG` is enabled.', 'wp-graphql-tec' ),
					'resolve'     => fn() => tribe_get_option( 'ticket-paypal-notify-url' ) ?: null,
				];
			}
		}

		register_graphql_fields(
			self::$type,
			array_merge(
				$fields,
				[
					'enabledPostTypes'         => [
						'type'        => [ 'list_of' => 'ContentTypeEnum' ],
						'description' => __( 'The post types that can have tickets.', 'wp-graphql-tec' ),
						'resolve'     => function() {
							$types = tribe_get_option( 'ticket-enabled-post-types' ) ?? [];

							// Remove Event post type if its not registered.
							if ( ! TEC::is_tec_loaded() ) {
								$types = array_diff( $types, [ 'tribe_events' ] );
							}
							return $types ?: null;
						},
					],
					'requireLoginToRSVP'       => [
						'type'        => 'Boolean',
						'description' => __( 'Whether a user must be logged in to RSVP.', 'wp-graphql-tec' ),
						'resolve'     => function() {
							$value = tribe_get_option( 'ticket-authentication-requirements' );
							return is_array( $value ) && in_array( 'event-tickets_rsvp', array_keys( $value ), true );
						},
					],
					'requireLoginToPurchase'   => [
						'type'        => 'Boolean',
						'description' => __( 'Whether a user must be logged in to RSVP.', 'wp-graphql-tec' ),
						'resolve'     => function() {
							$value = tribe_get_option( 'ticket-authentication-requirements' );
							return is_array( $value ) && in_array( 'event-tickets_all', $value, true );
						},
					],
					'isTicketsCommerceEnabled' => [
						'type'        => 'Boolean',
						'description' => __( 'Whether a user must be logged in to RSVP.', 'wp-graphql-tec' ),
						'resolve'     => fn() => $is_tickets_commerce_loaded,
					],
				]
			)
		);
	}
}
