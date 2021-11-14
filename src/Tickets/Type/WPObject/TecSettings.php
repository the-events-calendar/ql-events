<?php
/**
 * GraphQL Object Type - TecSettings
 *
 * @package WPGraphQL\TEC\Tickets\Type\WPObject
 * @since 0.0.1
 */

namespace WPGraphQL\TEC\Tickets\Type\WPObject;

use WPGraphQL\TEC\TEC;
use WPGraphQL\TEC\Tickets\Type\Enum\PaypalCurrencyCodeOptionsEnum;
use WPGraphQL\TEC\Tickets\Type\Enum\StockHandlingOptionsEnum;
use WPGraphQL\TEC\Tickets\Type\Enum\TicketFormLocationOptionsEnum;
use WPGraphQL\TEC\Utils\Utils;

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
	public static function register_fields() : void {
		$fields                     = [];
		$is_tickets_commerce_loaded = tec_tickets_commerce_is_enabled();

		if ( TEC::is_tec_loaded() ) {
			$fields['rsvpFormLocation']            = [
				'type'        => TicketFormLocationOptionsEnum::$type,
				'description' => __( 'The location of the RSVP ticket form. This setting only impacts events made with the classic editor. Defaults to `BEFORE_DETAILS`', 'wp-graphql-tec' ),
				'resolve'     => fn() =>  tribe_get_option( 'ticket-rsvp-form-location' ) ?: null,
			];
			$fields['commerceFormLocation']        = [
				'type'        => TicketFormLocationOptionsEnum::$type,
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
				'type'        => PaypalCurrencyCodeOptionsEnum::$type,
				'description' => __( 'Whether PayPal Sandbox mode for testing has been enabled.', 'wp-graphql-tec' ),
				'resolve'     => fn() => tribe_get_option( 'ticket-commerce-currency-code' ) ?: null,
			];
			$fields['stockHandling']                = [
				'type'        => StockHandlingOptionsEnum::$type,
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
							$types = Utils::get_enabled_post_types_for_tickets();
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
