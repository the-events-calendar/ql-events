<?php
/**
 * Order Model class
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
 * Class - Order
 */
class Order extends Post {
	/**
	 * The Order data
	 *
	 * @var array
	 */
	public array $order_data;

	/**
	 * Order constructor.
	 *
	 * @param WP_Post $post the post object.
	 *
	 * @throws Exception .
	 */
	public function __construct( WP_Post $post ) {
		parent::__construct( $post );

		if ( empty( $this->data->post_type ) || ! in_array( $this->data->post_type, array_keys( Utils::get_et_order_types() ), true ) ) {
			throw new Exception( __( 'The object returned is not an Order.', 'wp-graphql-tec' ) );
		}

		$order_data = tec_tc_get_order( $post, 'ARRAY_A' );

		if ( empty( $order_data ) || ! is_array( $order_data ) ) {
			throw new Exception( __( 'The array returned does not have any valid order data associated with it', 'wp-graphql-tec' ) );
		} else {
			$this->order_data = $order_data;
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
	 * Initializes the Order object.
	 */
	protected function init() {
		if ( empty( $this->fields ) ) {
			// Grab exceprt for future use.
			parent::init();
			$fields = [
				'currency'          => fn() : ?string => $this->order_data['currency'] ?? null,
				'eventDatabaseIds'  => fn() : ?array => ! empty( $this->order_data['events_in_order'] ) ? $this->order_data['events_in_order'] : null,
				'flagActionMarkers' => fn() : ?array => ! empty( $this->order_data['flag_action_markers'] ) ? $this->order_data['flag_action_markers'] : null,
				'formattedTotal'    => fn() : ?string => $this->order_data['formatted_total'] ?? null,
				'gateway'           => fn() : ?string => $this->order_data['gateway'] ?? null,
				'gatewayOrderId'    => fn() : ?string => $this->order_data['gateway_order_id'] ?? null,
				'gatewayPayload'    => fn() : ?array => ! empty( $this->order_data['gateway_payload'] ) ? $this->order_data['gateway_payload'] : null,
				'hash'              => fn() : ?string => $this->order_data['hash'] ?? null,
				'id'                => fn() : ?string => ! empty( $this->data->ID ) ? Relay::toGlobalId( $this->data->post_type, (string) $this->data->ID ) : null,
				'items'             => fn() : ?array => ! empty( $this->order_data['items'] ) ? $this->order_data['items'] : null,
				'providerClass'     => fn() : ?string => $this->order_data['provider_slug'] ?? null,
				'provider'          => fn() : ?string => $this->order_data['provider'] ?? null,
				'purchaser'         => fn() : ?array => ! empty( $this->order_data['purchaser'] ) ? $this->order_data['purchaser'] : null,
				'purchaserEmail'    => fn() : ?string => $this->order_data['purchaser_email'] ?? null,
				'purchaserName'     => fn() : ?string => $this->order_data['purchaser_name'] ?? null,
				'purchaseTime'      => fn() : ?string => $this->order_data['purchase_time'] ?? null,
				'status'            => fn() : ?string => ! empty( $this->data->post_status ) ? $this->data->post_status : null,
				'statusLog'         => fn() : ?array => ! empty( $this->order_data['status_log'] ) ? $this->order_data['status_log'] : null,
				'ticketDatabaseIds' => fn() : ?array => ! empty( $this->order_data['tickets_in_order'] ) ? $this->order_data['tickets_in_order'] : null,
				'totalValue'        => fn() : ?int => $this->order_data['total_value'] ?? null,
				'type'              => fn() : ?string => $this->data->post_type ?: null,
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
			$this->fields = apply_filters( 'graphql_tec_order_model_fields', $this->fields, $this );
		}
	}
}
