<?php
/**
 * Ticket Model class
 *
 * @package \WPGraphQL\TEC\Tickets\Models
 * @since   0.0.1
 */

namespace WPGraphQL\TEC\Tickets\Model;

use Exception;
use GraphQLRelay\Relay;
use WP_Post;
use WPGraphQL\Model\Post;
use WPGraphQL\TEC\Utils\Utils;

/**
 * Class - Attendee
 */
class Attendee extends Post {
	/**
	 * The Attendee provider.
	 *
	 * @var mixed
	 */
	public $provider;

	/**
	 * The attached event id.
	 *
	 * @var int
	 */
	public int $event_id;

	/**
	 * The attendee data
	 *
	 * @var array
	 */
	public $attendee_data;

	/**
	 * Ticket constructor.
	 *
	 * @param WP_Post $post the post object.
	 *
	 * @throws Exception .
	 */
	public function __construct( WP_Post $post ) {
		parent::__construct( $post );

		if ( ! isset( $this->data->post_type ) || ! isset( $this->data->ID ) || ! in_array( $this->data->post_type, array_keys( Utils::get_et_attendee_types() ), true ) ) {
			throw new Exception( __( 'The object returned is not an Attendee.', 'wp-graphql-tec' ) );
		}

		$this->provider = tribe_tickets_get_ticket_provider( $this->data->ID );

		if ( false === $this->provider ) {
			throw new Exception(
				__( 'The object returned does not have a valid ticket provider.', 'wp-graphql-tec' )
			);
		}

		$this->event_id = (int) tribe_tickets_get_event_ids( $this->data->ID )[0];

		$attendee_data = $this->provider->get_attendee( $this->data->ID );

		if ( empty( $attendee_data ) ) {
			throw new Exception( __( 'The object returned does not have any valid attendee data associated with it', 'wp-graphql-tec' ) );
		} else {
			$this->attendee_data = $attendee_data;
		}
	}


	/**
	 * {@inheritDoc}
	 */
	public function setup() {
		remove_action( 'the_post', [ tribe( \Tribe\Events\Views\V2\Hooks::class ), 'manage_sensitive_info' ] );

		parent::setup();
	}

	/**
	 * Initializes the Ticket object.
	 */
	protected function init() {
		if ( empty( $this->fields ) ) {
			// Grab exceprt for future use.
			parent::init();
			$fields = [
				'attendeeMeta'     => fn()  => $this->attendee_data['attendee_meta'] ?: null,
				'checkInStatus'    => fn() : ?string => $this->attendee_data['check_in'] ?: null,
				'eventId'          => fn() : ?int => $this->attendee_data['event_id'],
				'holderEmail'      => fn() : ?string => $this->attendee_data['holder_email'] ?: null,
				'holderName'       => fn() : ?string => $this->attendee_data['holder_name'] ?: null,
				'id'               => fn() : ?string => ! empty( $this->data->ID ) ? Relay::toGlobalId( $this->data->post_type, (string) $this->data->ID ) : null,
				'isLegacyAttendee' => fn() : bool => $this->attendee_data['is_legacy_attendee'] ?? false,
				'isOptout'         => fn() : ?bool => $this->attendee_data['optout'] ?? null,
				'isPurchaser'      => fn() : bool => $this->attendee_data['is_purchaser'] ?? true,
				'isSubscribed'     => fn() : ?bool => $this->attendee_data['is_subscribed'] ?? null,
				'isTicketSent'     => fn() : ?bool => $this->attendee_data['ticket_sent'] ?? null,
				'orderId'          => fn() : ?int => $this->attendee_data['order_id'] ?? null,
				'orderStatus'      => fn() : ?string => $this->attendee_data['order_status'] ?? null,
				'orderStatusLabel' => fn() : ?string => $this->attendee_data['order_status_label'] ?? null,
				'productId'        => fn() : ?int => $this->attendee_data['product_id'] ?? null,
				'provider'         => fn()  => $this->provider,
				'providerClass'    => fn() : ?string => $this->attendee_data['provider_slug'] ?? null,
				'purchaserEmail'   => fn() : ?string => $this->attendee_data['purchaser_email'] ?? null,
				'purchaserName'    => fn() : ?string => $this->attendee_data['purchaser_name'] ?? null,
				'purchaseTime'     => fn() : ?string => $this->attendee_data['purchase_time'] ?? null,
				'qrTicketId'       => fn() : ?string => $this->attendee_data['qr_ticket_id'] ?? null,
				'securityCode'     => fn() : ?string => $this->attendee_data['security_code'] ?? null,
				'ticketId'         => function() : ?int {
					$ticket_id = $this->attendee_data['ticket_id'];
					// Use product id if ticket id is the same as attendee.
					if ( $this->data->ID === $ticket_id ) {
						$ticket_id = $this->attendee_data['product_id'];
					}

					return $ticket_id ?? null;
				},
				'ticketName'       => fn() : ?string => $this->attendee_data['ticket_name'] ?? null,
				'type'             => fn() : string => $this->data->post_type,
				'userId'           => fn() : ?bool => $this->attendee_data['user_id'] ?? null,
			];

			$this->fields = array_merge( $this->fields, $fields );

			/**
			 * Filters the model fields.
			 *
			 * Useful for adding fields to a model when an extension.
			 *
			 * @param array $fields The fields registered to the model.
			 * @param __CLASS__ $model The current model.
			 */
			$this->fields = apply_filters( 'graphql_tec_attendee_model_fields', $this->fields, $this );
		}
	}
}
