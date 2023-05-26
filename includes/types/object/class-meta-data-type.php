<?php
/**
 * WPObject Type - Meta_Data_Type
 *
 * Registers MetaData type and queries
 *
 * @package WPGraphQL\QL_Events\Type\WPObject
 * @since   0.0.1
 */

namespace WPGraphQL\QL_Events\Type\WPObject;

use WPGraphQL\QL_Events\QL_Events;

/**
 * Class Meta_Data_Type
 */
class Meta_Data_Type {

	/**
	 * Register Order type and queries to the WPGraphQL schema
	 *
	 * @since 0.0.1
	 *
	 * @return void
	 */
	public static function register() {
		register_graphql_object_type(
			'MetaData',
			[
				'description' => __( 'Extra data defined on the WC object', 'ql-events' ),
				'fields'      => [
					'id'    => [
						'type'        => 'ID',
						'description' => __( 'Meta ID.', 'ql-events' ),
						'resolve'     => function ( $source ) {
							return ! empty( $source->id ) ? $source->id : null;
						},
					],
					'key'   => [
						'type'        => [ 'non_null' => 'String' ],
						'description' => __( 'Meta key.', 'ql-events' ),
						'resolve'     => function ( $source ) {
							return ! empty( $source->key ) ? (string) $source->key : null;
						},
					],
					'value' => [
						'type'        => 'String',
						'description' => __( 'Meta value.', 'ql-events' ),
						'resolve'     => function ( $source ) {
							return ! empty( $source->value ) ? (string) $source->value : null;
						},
					],
				],
			]
		);
	}
}
