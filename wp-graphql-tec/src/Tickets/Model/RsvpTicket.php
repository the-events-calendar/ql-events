<?php
/**
 * Ticket Model class
 *
 * @package \WPGraphQL\TEC\Tickets\Model
 * @since   0.0.1
 */

namespace WPGraphQL\TEC\Tickets\Model;

use Tribe__Tickets__RSVP;

/**
 * Class - Ticket
 */
class RsvpTicket extends Ticket {
	/**
	 * The Ticker provider to use.
	 *
	 * @var Tribe__Tickets__RSVP $provider the ORM provider.
	 */
	public $provider;

	/**
	 * Initializes the Ticket object.
	 */
	protected function init() {
		if ( empty( $this->fields ) ) {
			// Grab exceprt for future use.
			parent::init();
			$fields = [
				// @todo add support for attendee counts.
				'attendeesNotGoing' => fn() :?int => null, // phpcs:ignore $this->provider->get_attendees_count_not_going( $this->event_id ),
				'attendeesGoing'    => fn() :?int => null, // phpcs:ignore $this->provider->get_attendees_count_going( $this->event_id ),
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
			$this->fields = apply_filters( 'graphql_tec_rsvp_ticket_model_fields', $this->fields, $this );
		}
	}
}
