<?php

namespace QL_Events\Test\Factories;

use Tribe__Tickets__Global_Stock as Global_Stock;
use Tribe__Utils__Array as Utils_Array;

class Ticket extends \WP_UnitTest_Factory_For_Post {

	/**
	 * Generates ticket.
	 *
	 * @param string $provider  Provider class to use.
	 * @param int    $post_id   The ID of the post this ticket should be related to.
	 * @param int    $price
	 * @param array  $overrides An array of values to override the default and random generation arguments.
	 *
	 * @return int|false The new ticket ID or false if not saved.
	 */
	protected function create_ticket( $provider, $post_id, $price = 1, array $overrides = [] ) {
		/** @var \Tribe__Tickets__Tickets $provider_class */
		$provider_class = tribe( $provider );

		$post_id = absint( $post_id );

		$data = [
			'ticket_name'             => "Test ticket for {$post_id}",
			'ticket_description'      => "Test ticket description for {$post_id}",
			'ticket_show_description' => 1,
			'ticket_price'            => absint( $price ),
			'ticket_start_date'       => '2020-01-02',
			'ticket_start_time'       => '08:00:00',
			'ticket_end_date'         => '2050-03-01',
			'ticket_end_time'         => '20:00:00',
			'ticket_sku'              => "TEST-TKT-{$post_id}",
			'tribe-ticket'            => [
				'mode'     => Global_Stock::OWN_STOCK_MODE,
				'capacity' => 100,
			],
		];

		$data = array_merge( $data, $overrides );

		return $provider_class->ticket_add( $post_id, $data );
	}

	/**
	 * Generate multiple tickets for a post - the tickets need not be identical.
	 *
	 * @param string $provider      Provider class to use.
	 * @param int    $post_id       The ID of the post these tickets should be related to.
	 * @param array  $tickets       An array of tickets. Ech ticket must be an array.
	 *                              Any data in the array will override the defaults.
	 *                              This should be in the same format as the "overrides" you
	 *                              would send to create_ticket() above.
	 *
	 * @return array An array of the generated ticket IDs.
	 */
	protected function create_distinct_tickets( $provider, $post_id, array $tickets ) {
		$global_sales       = 0;
		$global_stock       = new Global_Stock( $post_id );
		$has_global_tickets = false;
		$ticket_ids         = [];

		foreach ( $tickets as $ticket ) {
			// Randomize price.
			try {
				$price = $ticket['price'] ?? random_int( 1, 10 );
			} catch ( \Exception $exception ) {
				$price = 5;
			}

			// Create ticket.
			$ticket_ids[] = $this->create_ticket( $provider, $post_id, $price, $ticket );
		}

		return $ticket_ids;
	}

	/**
	 * Generates multiple identical tickets for a post.
	 *
	 * @param string $provider  Provider class to use.
	 * @param int    $count     The number of tickets to create
	 * @param int    $post_id   The ID of the post this ticket should be related to.
	 * @param array  $overrides An array of values to override the default and random generation arguments.
	 *
	 * @return array An array of the generated ticket IDs.
	 */
	protected function create_many_tickets( $provider, $count, $post_id, array $overrides = [] ) {
		$ticket_data = [];

		for ( $i = 0; $i < $count; $i ++ ) {
			$ticket_data[] = $overrides;
		}

		return $this->create_distinct_tickets( $provider, $post_id, $ticket_data );
	}

	/**
	 * Get the ticket provider class.
	 *
	 * @return string Ticket provider class.
	 */
	protected function get_rsvp_ticket_provider() {
		return 'tickets.rsvp';
	}

	/**
	 * Generates an RSVP ticket for a post.
	 *
	 * @param int   $post_id   The ID of the post this ticket should be related to.
	 * @param array $overrides An array of values to override the default and random generation arguments.
	 *
	 * @return int The generated ticket post ID.
	 */
	public function create_rsvp_ticket( $post_id, array $overrides = [] ) {
		$factory = $this->factory ?? $this->factory();

		$meta_input = isset( $overrides['meta_input'] ) && \is_array( $overrides['meta_input'] )
			? $overrides['meta_input']
			: [];

		unset( $overrides['meta_input'] );

		/** @var \Tribe__Tickets__RSVP $rsvp */
		$rsvp             = tribe( 'tickets.rsvp' );
		$capacity         = Utils_Array::get( $meta_input, '_capacity', 100 );
		$sales            = Utils_Array::get( $meta_input, 'total_sales', 0 );

		$calculated_stock = -1 === $capacity ? null : ( $capacity - $sales );
		$manage_stock     = -1 === $capacity ? 'no' : 'yes';

		// Unlike tickets, we don't store '-1' for unlimited RSVP.
		if ( -1 === $capacity || '' === $capacity ) {
			$capacity = '-1';
		}

		unset( $meta_input['_capacity'], $meta_input['_stock'] );

		$merged_meta_input = array_merge(
			[
				'_tribe_rsvp_for_event'                          => $post_id,
				tribe( 'tickets.handler' )->key_capacity         => $capacity,
				'_manage_stock'                                  => 'yes',
				'_ticket_start_date'                             => date( 'Y-m-d H:i:s', strtotime( '-1 day' ) ),
				'_ticket_end_date'                               => date( 'Y-m-d H:i:s', strtotime( '+1 day' ) ),
			],
			$meta_input
		);

		// We don't set stock for unlimited rsvps
		if ( tribe_is_truthy( $manage_stock ) ) {
			$merged_meta_input[ '_stock' ] = $calculated_stock;
		}

		// if we have sales, set them
		if ( ! empty( $sales ) ) {
			$merged_meta_input['total_sales' ] = $sales;
		}

		// if the ticket start and/or end date(s) are set to empty values they should
		// not be set
		foreach ( [ '_ticket_start_date', '_ticket_end_date' ] as $key ) {
			if ( empty( $merged_meta_input[ $key ] ) ) {
				unset( $merged_meta_input[ $key ] );
			}
		}

		$ticket_id = $factory->post->create( array_merge(
				[
					'post_title'   => "Test RSVP ticket for " . $post_id,
					'post_content' => "Test RSVP ticket description for " . $post_id,
					'post_excerpt' => "Ticket RSVP ticket excerpt for " . $post_id,
					'post_type'    => $rsvp->ticket_object,
					'meta_input'   => $merged_meta_input,
				], $overrides )
		);

		// Clear the cache.
		$rsvp->clear_ticket_cache_for_post( $post_id );

		return $ticket_id;
	}

	public function create_many_rsvp_tickets( int $count, int $post_id, array $overrides = [] ) {
		return array_map( function () use ( $post_id, $overrides ) {
			return $this->create_rsvp_ticket( $post_id, $overrides );
		}, range( 1, $count ) );
	}

	/**
	 * Get the ticket provider class.
	 *
	 * @return string Ticket provider class.
	 */
	protected function get_paypal_ticket_provider() {
		return 'tickets.commerce.paypal';
	}

	/**
	 * Generates a PayPal ticket for a post.
	 *
	 * @param int   $post_id   The ID of the post this ticket should be related to.
	 * @param int   $price     Ticket price.
	 * @param array $overrides An array of values to override the default and random generation arguments.
	 *
	 * @return int The generated ticket post ID.
	 */
	public function create_paypal_ticket( $post_id, $price = 1, array $overrides = [] ) {
		$post_id = absint( $post_id );

		$data = [
			'ticket_name'        => "Test PayPal ticket for {$post_id}",
			'ticket_description' => "Test PayPal ticket description for {$post_id}",
			'ticket_price'       => $price,
		];

		$data = array_merge( $data, $overrides );

		return $this->create_ticket( $this->get_paypal_ticket_provider(), $post_id, $price, $data );
	}

	/**
	 * Generates multiple identical PayPal tickets for a post.
	 *
	 * @param int   $count     The number of tickets to create
	 * @param int   $post_id   The ID of the post this ticket should be related to.
	 * @param array $overrides An array of values to override the default and random generation arguments.
	 *
	 * @return array An array of the generated ticket post IDs.
	 */
	public function create_many_paypal_tickets( $count, $post_id, array $overrides = [] ) {
		return $this->create_many_tickets( $this->get_paypal_ticket_provider(), $count, $post_id, $overrides );
	}

	/**
	 * Generate multiple tickets for a post - the tickets need not be identical.
	 *
	 * @param int   $post_id        The ID of the post these tickets should be related to.
	 * @param array $tickets        An array of tickets. Ech ticket must be an array.
	 *                              Any data in the array will override the defaults.
	 *                              This should be in the same format as the "overrides" you
	 *                              would send to create_paypal_ticket_basic() above.
	 *
	 * @return array An array of the generated ticket post IDs.
	 */
	protected function create_distinct_paypal_tickets( $post_id, array $tickets ) {
		return $this->create_distinct_tickets( $this->get_paypal_ticket_provider(), $post_id, $tickets );
	}

	/**
	 * Generates a PayPal ticket for a post.
	 *
	 * @deprecated Use create_paypal_ticket() going forward instead.
	 *
	 * @param int   $post_id   The ID of the post this ticket should be related to.
	 * @param int   $price     Ticket price.
	 * @param array $overrides An array of values to override the default and random generation arguments.
	 *
	 * @return int The generated ticket post ID.
	 *
	 */
	public function create_paypal_ticket_basic( $post_id, $price = 1, array $overrides = [] ) {
		$factory      = $this->factory ?? $this->factory();
		$global_stock = new Global_Stock( $post_id );
		$meta_input   = isset( $overrides['meta_input'] ) && is_array( $overrides['meta_input'] ) ? $overrides['meta_input'] : [];

		$capacity = Utils_Array::get( $meta_input, '_capacity', 100 );
		$sales    = Utils_Array::get( $meta_input, 'total_sales', 0 );

		// We don't set stock for unlimited tickets, take sales into account when setting stock.
		$calculated_stock = - 1 === $capacity ? null : ( $capacity - $sales );
		$manage_stock     = - 1 === $capacity ? 'no' : 'yes';
		$stock            = Utils_Array::get( $meta_input, '_stock', $calculated_stock );

		// Allow overriding the stock mode
		$stock_mode = Utils_Array::get( $meta_input, $global_stock::TICKET_STOCK_MODE, $global_stock::OWN_STOCK_MODE );

		unset( $overrides['meta_input'] );

		$default_meta_input = [
			'_tribe_tpp_for_event' => $post_id,
			'_price'               => $price,
			'_manage_stock'        => $manage_stock,
			'_ticket_start_date'   => date( 'Y-m-d H:i:s', strtotime( '-1 day' ) ),
			'_ticket_end_date'     => date( 'Y-m-d H:i:s', strtotime( '+1 day' ) ),
		];

		// We don't set stock or stock mode for unlimited tickets
		if ( tribe_is_truthy( $manage_stock ) ) {
			$default_meta_input['_stock']                           = $stock;
			$default_meta_input[ $global_stock::TICKET_STOCK_MODE ] = $stock_mode;
		}

		// We only set capacity for non-shared tickets
		if ( $global_stock::OWN_STOCK_MODE === $stock_mode || - 1 === $capacity ) {
			$default_meta_input[ tribe( 'tickets.handler' )->key_capacity ] = $capacity;
		}

		// if we have sales, set them
		if ( ! empty( $sales ) ) {
			$default_meta_input['total_sales'] = $sales;
		}

		$defaults = [
			'post_title'   => "Test PayPal ticket for " . $post_id,
			'post_content' => "Test PayPal ticket description for " . $post_id,
			'post_excerpt' => "Ticket PayPal ticket excerpt for " . $post_id,
			'post_type'    => tribe( 'tickets.commerce.paypal' )->ticket_object,
			'meta_input'   => array_merge( $default_meta_input, $meta_input ),
		];

		$ticket_id = $factory->post->create( array_merge( $defaults, $overrides ) );

		// Get provider key name.
		$provider_key = tribe( 'tickets.handler' )->key_provider_field;

		// Update provider key for post.
		update_post_meta( $post_id, $provider_key, 'Tribe__Tickets__Commerce__PayPal__Main' );

		// Clear the cache.
		tribe( $this->get_paypal_ticket_provider() )->clear_ticket_cache_for_post( $post_id );

		return $ticket_id;
	}

	/**
	 * Generates multiple identical PayPal tickets for a post.
	 *
	 * @deprecated Use create_many_paypal_tickets() going forward instead.
	 *
	 * @param int   $count     The number of tickets to create
	 * @param int   $post_id   The ID of the post this ticket should be related to.
	 * @param array $overrides An array of values to override the default and random generation arguments.
	 *
	 * @return array An array of the generated ticket post IDs.
	 */
	public function create_many_paypal_tickets_basic( $count, $post_id, array $overrides = [] ) {
		$ticket_data = [];

		for ( $i = 0; $i < $count; $i ++ ) {
			$ticket_data[] = $overrides;
		}

		return $this->create_distinct_paypal_tickets_basic( $post_id, $ticket_data );
	}

	/**
	 * Generate multiple tickets for a post - the tickets need not be identical.
	 * Handles global stock as well.
	 *
	 * @deprecated Use create_distinct_paypal_tickets() going forward instead.
	 *
	 * @param int   $post_id        The ID of the post these tickets should be related to.
	 * @param array $tickets        An array of tickets. Ech ticket must be an array.
	 *                              Any data in the array will override the defaults.
	 *                              This should be in the same format as the "overrides" you
	 *                              would send to create_paypal_ticket_basic() above.
	 * @param int   $global_qty     The global quantity to set, if needed. Will attempt to set
	 *                              intelligently if not provided when there are shared tickets.
	 *
	 * @return array An array of the generated ticket post IDs.
	 */
	protected function create_distinct_paypal_tickets_basic( $post_id, array $tickets, $global_qty = 0 ) {
		$global_sales       = 0;
		$global_stock       = new Global_Stock( $post_id );
		$has_global_tickets = false;
		$ticket_ids         = [];

		foreach ( $tickets as $ticket ) {
			// Randomize price.
			try {
				$price = $ticket['price'] ?? random_int( 1, 10 );
			} catch ( \Exception $exception ) {
				$price = 5;
			}

			// Create ticket.
			$ticket_id    = $this->create_paypal_ticket_basic( $post_id, $price, $ticket );
			$ticket_ids[] = $ticket_id;

			// Handle tickets with global stock
			$mode = $ticket['meta_input'][ $global_stock::TICKET_STOCK_MODE ] ?? $global_stock::OWN_STOCK_MODE;

			if ( in_array( $mode, [ $global_stock::CAPPED_STOCK_MODE, $global_stock::GLOBAL_STOCK_MODE ], true ) ) {
				$has_global_tickets = true;

				// Get ticket capacity.
				$cap = $ticket['meta_input']['_capacity'] ?? 0;

				// Handle passed sales.
				$sales = $ticket['meta_input']['total_sales'] ?? 0;

				if ( ! empty( $sales ) ) {
					$global_sales += $sales;
				}

				if ( $global_qty < $cap ) {
					// ensure we have enough cap to cover all tickets
					$global_qty = $cap;
				}
			}
		}

		// Handle event meta for global stock
		$global_stock->enable( $has_global_tickets );

		if ( $has_global_tickets ) {
			$global_qty = $global_qty ?? 100;

			/** @var Tribe__Tickets__Tickets_Handler $tickets_handler */
			$tickets_handler = tribe( 'tickets.handler' );
			update_post_meta( $post_id, $global_stock::TICKET_STOCK_CAP, $global_qty );
			update_post_meta( $post_id, $global_stock::GLOBAL_STOCK_LEVEL, $global_qty - $global_sales );
			update_post_meta( $post_id, $tickets_handler->key_capacity, $global_qty );
		}

		return $ticket_ids;
	}

	/**
	 * Get the ticket provider class.
	 *
	 * @return string Ticket provider class.
	 */
	protected function get_woocommerce_ticket_provider() {
		return 'tickets-plus.commerce.woo';
	}

	/**
	 * Generates a WooCommerce ticket for a post.
	 *
	 * @param int   $post_id   The ID of the post this ticket should be related to.
	 * @param int   $price     Ticket price.
	 * @param array $overrides An array of values to override the default and random generation arguments.
	 *
	 * @return int The generated ticket post ID.
	 */
	public function create_woocommerce_ticket( $post_id, $price, array $overrides = [] ) {
		$post_id = absint( $post_id );

		$data = [
			'ticket_name'        => "Test WooCommerce ticket for {$post_id}",
			'ticket_description' => "Test WooCommerce ticket description for {$post_id}",
			'ticket_price'       => $price,
		];

		$data = array_merge( $data, $overrides );

		return $this->create_ticket( $this->get_woocommerce_ticket_provider(), $post_id, $price, $data );
	}

	/**
	 * Generates multiple identical WooCommerce tickets for a post.
	 *
	 * @param int   $count     The number of tickets to create
	 * @param int   $post_id   The ID of the post this ticket should be related to.
	 * @param array $overrides An array of values to override the default and random generation arguments.
	 *
	 * @return array An array of the generated ticket post IDs.
	 */
	public function create_many_woocommerce_tickets( $count, $post_id, array $overrides = [] ) {
		return $this->create_many_tickets( $this->get_woocommerce_ticket_provider(), $count, $post_id, $overrides );
	}

	/**
	 * Generates a WooCommerce ticket for a post.
	 *
	 * @deprecated Use create_woocommerce_ticket() going forward instead.
	 *
	 * @param int   $post_id   The ID of the post this ticket should be related to.
	 * @param int   $price
	 * @param array $overrides An array of values to override the default and random generation arguments.
	 *
	 * @return int The generated ticket post ID.
	 */
	public function create_woocommerce_ticket_basic( $post_id, $price, array $overrides = [] ) {
		$factory = $this->factory ?? $this->factory();

		$meta_input = isset( $overrides['meta_input'] ) && \is_array( $overrides['meta_input'] ) ? $overrides['meta_input'] : [];

		$capacity = \Tribe__Utils__Array::get( $meta_input, '_capacity', 100 );
		$stock    = \Tribe__Utils__Array::get( $meta_input, '_stock', $capacity );

		unset( $overrides['meta_input'] );

		/** @var \Tribe__Tickets_Plus__Commerce__WooCommerce__Main $main */
		$main      = tribe( 'tickets-plus.commerce.woo' );
		$ticket_id = $factory->post->create( array_merge( [
			'post_title'   => "Test WooCommerce ticket for {$post_id}",
			'post_content' => "Test WooCommerce ticket description for {$post_id}",
			'post_excerpt' => "Ticket WooCommerce ticket excerpt for {$post_id}",
			'post_type'    => $main->ticket_object,
			'meta_input'   => array_merge( [
				$main->event_key                                 => $post_id,
				'_price'                                         => $price,
				'_regular_price'                                 => $price,
				'_stock'                                         => $stock,
				tribe( 'tickets.handler' )->key_capacity         => $capacity,
				'_manage_stock'                                  => 'yes',
				'_ticket_start_date'                             => date( 'Y-m-d H:i:s', strtotime( '-1 day' ) ),
				'_ticket_end_date'                               => date( 'Y-m-d H:i:s', strtotime( '+1 day' ) ),
				\Tribe__Tickets__Global_Stock::TICKET_STOCK_MODE => 'own',
			], $meta_input ),
		], $overrides ) );

		// Get provider key name.
		$provider_key = tribe( 'tickets.handler' )->key_provider_field;

		// Update provider key for post.
		update_post_meta( $post_id, $provider_key, 'Tribe__Tickets_Plus__Commerce__WooCommerce__Main' );

		// Clear the cache.
		tribe( $this->get_woocommerce_ticket_provider() )->clear_ticket_cache_for_post( $post_id );

		return $ticket_id;
	}

	/**
	 * Generates multiple identical WooCommerce tickets for a post.
	 *
	 * @deprecated Use create_many_woocommerce_tickets() going forward instead.
	 *
	 * @param int   $count     The number of tickets to create
	 * @param int   $post_id   The ID of the post this ticket should be related to.
	 * @param array $overrides An array of values to override the default and random generation arguments.
	 *
	 * @return array An array of the generated ticket post IDs.
	 */
	public function create_many_woocommerce_tickets_basic( $count, $post_id, array $overrides = [] ) {
		return array_map( function () use ( $post_id, $overrides ) {
			$price = $overrides['price'] ?? random_int( 1, 5 );

			return $this->create_woocommerce_ticket_basic( $post_id, $price, $overrides );
		}, range( 1, $count ) );
	}

	/**
	 * Add ARI fields to ticket.
	 *
	 * @param int     $ticket_id        Ticket ID.
	 * @param boolean $include_required Whether to include required fields.
	 */
	public function add_attendee_meta_fields_to_ticket( $ticket_id, $data = [] ) {
		/** @var \Tribe__Tickets_Plus__Meta $meta */
		$meta = \Tribe__Tickets_Plus__Main::instance()->meta();

		$ticket_object = \Tribe__Tickets__Tickets::load_ticket_object( $ticket_id );

		$meta->save_meta( $ticket_object->get_event_id(), $ticket_object, $data );
	}
}
