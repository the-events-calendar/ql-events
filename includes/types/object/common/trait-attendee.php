<?php
/**
 * Defines common fields for Ticket Events' Attendee types
 *
 * @package \WPGraphQL\QL_Events\Type\WPObject
 * @since   0.0.1
 */

namespace WPGraphQL\QL_Events\Type\WPObject;

use WPGraphQL\AppContext;
use WPGraphQL\Data\DataSource;

/**
 * Trait - Attendee
 */
trait Attendee {
	/**
	 * Define common Attendee fields
	 *
	 * @since 0.0.1
	 *
	 * @return array
	 */
	public static function fields() {
		return [
			'event'               => [
				'type'        => 'Event',
				'description' => __( 'Event attendee is excepted to attend.', 'ql-events' ),
				'resolve'     => function( $source, array $args, AppContext $context ) {
					$event_id = get_post_meta( $source->ID, self::manager()::ATTENDEE_EVENT_KEY, true );
					return ! empty( $event_id )
						? DataSource::resolve_post_object( $event_id, $context )
						: null;
				},
			],
			'checkedIn'           => [
				'type'        => 'Boolean',
				'description' => __( 'Has attendee checked into the event.', 'ql-events' ),
				'resolve'     => function( $source, array $args, AppContext $context ) {
					$checked_in = get_post_meta( $source->ID, self::manager()->checkin_key, true );
					return ! empty( $checked_in ) ? $checked_in : false;
				},
			],
			'securityCode'        => [
				'type'        => 'String',
				'description' => __( 'Security code on attendee\'s ticket.', 'ql-events' ),
				'resolve'     => function( $source, array $args, AppContext $context ) {
					$security_code = get_post_meta( $source->ID, self::manager()->security_code, true );
					return ! empty( $security_code ) ? $security_code : null;
				},
			],
			'paidPrice'           => [
				'type'        => 'String',
				'description' => __( 'Security code on attendee\'s ticket.', 'ql-events' ),
				'resolve'     => function( $source, array $args, AppContext $context ) {
					$paid_price = get_post_meta( $source->ID, '_paid_price', true );
					return ! empty( $paid_price ) ? $paid_price : 'free';
				},
			],
			'priceCurrencySymbol' => [
				'type'        => 'String',
				'description' => __( 'Security code on attendee\'s ticket.', 'ql-events' ),
				'resolve'     => function( $source, array $args, AppContext $context ) {
					$price_currency_symbol = get_post_meta( $source->ID, '_price_currency_symbol', true );
					return ! empty( $price_currency_symbol ) ? $price_currency_symbol : null;
				},
			],
		];
	}
}
