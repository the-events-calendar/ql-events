<?php
/**
 * PurchasableTicket Model class
 *
 * @package \WPGraphQL\TEC\Tickets\Model
 * @since   0.0.1
 */

namespace WPGraphQL\TEC\Tickets\Model;

/**
 * Class - PurchasableTicket
 */
class PurchasableTicket extends Ticket {


	/**
	 * Initializes the Ticket object.
	 */
	protected function init() {
		if ( empty( $this->fields ) ) {
			// Grab exceprt for future use.
			parent::init();
			$fields = [
				'isOnSale'          => fn() : ?bool => $this->ticket_data->on_sale ?? null,
				'priceSuffix'       => fn() : ?string => $this->ticket_data->price_suffix ?: null,
				'quantityCancelled' => fn() : int => $this->ticket_data->qty_cancelled(),
				'quantityPending'   => fn() : int => $this->ticket_data->qty_pending(),
				'quantityRefunded'  => fn() : int => $this->ticket_data->qty_refunded(),
				'regularPrice'      => fn() : ?float => $this->ticket_data->regular_price ?: null,
				'sku'               => fn() : ?string => $this->ticket_data->sku ?: null,
				'stockCap'          => fn() : int => $this->ticket_data->global_stock_cap(),
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
			$this->fields = apply_filters( 'graphql_tec_purchasable_ticket_model_fields', $this->fields, $this );
		}
	}
}
