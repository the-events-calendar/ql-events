<?php
/**
 * Ticket fieldset types.
 *
 * Registers ticket custom fieldsets
 *
 * @package \WPGraphQL\QL_Events\Type\WPObject
 * @since   0.1.0
 */

namespace WPGraphQL\QL_Events\Type\WPObject;

/**
 * Class Ticket_Fieldset_Types
 */
class Ticket_Fieldset_Types {
	/**
	 * Registers type.
	 */
	public static function register() {
		$fieldsets = tribe( 'tickets-plus.meta' )->meta_fieldset()->get_fieldsets();

		foreach( $fieldsets as $fieldset ) {
			print_r( $fieldset );
			register_graphql_object_type(
				'Fieldset' . ucfirst( graphql_format_field_name( $fieldset->post_title ) ),
				array(
					'interfaces'  => array( 'TicketFieldset' ),
					'description' => $fieldset->post_content,
					'fields'      => array(
						// 'test' => array(
						// 	'type'    => 'String',
						// 	'resolve' => function() use( $fieldset ) {
						// 		return wp_json_encode( $fieldset );
						// 	}
						// )
					)
				)
			);
		}
	}
}
