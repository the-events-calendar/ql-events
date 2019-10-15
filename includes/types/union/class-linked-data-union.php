<?php
/**
 * WPUnion Type - LinkedDataUnion
 *
 * Resolves object linked data types
 *
 * @package \WPGraphQL\Extensions\QL_Events\Type\WPUnion
 * @since   0.0.1
 */

namespace WPGraphQL\Extensions\QL_Events\Type\WPUnion;

use WPGraphQL\TypeRegistry;

/**
 * Class Linked_Data_Union
 */
class Linked_Data_Union {
	/**
	 * Registers LinkedDataUnion type.
	 */
	public static function register() {
		$possible_types = array(
			TypeRegistry::get_type( 'EventLinkedData' ),
			TypeRegistry::get_type( 'OrganizerLinkedData' ),
			TypeRegistry::get_type( 'VenueLinkedData' ),
		);
		register_graphql_union_type(
			'LinkedDataUnion',
			array(
				'types'       => $possible_types,
				'resolveType' => function( $value ) {
					
				},
			)
		);
	}
}
