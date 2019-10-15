<?php
/**
 * WPObject Type - TicketLinkedData
 *
 * Resolves ticket linked data
 *
 * @package \WPGraphQL\Extensions\QL_Events\Type\WPObject
 * @since   0.0.1
 */

namespace WPGraphQL\Extensions\QL_Events\Type\WPObject;

/**
 * Class Ticket_Linked_Data
 */
class Ticket_Linked_Data_Type {
	/**
	 * Registers TicketLinkedData type.
	 */
	public static function register() {
		register_graphql_object_type(
			'TicketLinkedData',
			array(
				'description' => __( 'Ticket JSON-LD data', 'ql-events' ),
				'fields'      => array(
					'type'          => array(
						'type'    => 'String',
						'resolve' => function( $source ) {
							return ! empty( $source->{'@type'} ) ? $source->{'@type'} : null;
						},
					),
					'url'           => array(
						'type'    => 'String',
						'resolve' => function( $source ) {
							return ! empty( $source->url ) ? $source->url : null;
						},
					),
					'price'         => array(
						'type'    => 'String',
						'resolve' => function( $source ) {
							return ! empty( $source->price ) ? $source->price : null;
						},
					),
					'category'      => array(
						'type'    => 'String',
						'resolve' => function( $source ) {
							return ! empty( $source->category ) ? $source->category : null;
						},
					),
					'availability'  => array(
						'type'    => 'String',
						'resolve' => function( $source ) {
							return ! empty( $source->availability ) ? $source->availability : null;
						},
					),
					'priceCurrency' => array(
						'type'    => 'String',
						'resolve' => function( $source ) {
							//@codingStandardsIgnoreLine.
							return ! empty( $source->priceCurrency ) ? $source->priceCurrency : null;
						},
					),
					'validFrom'     => array(
						'type'    => 'String',
						'resolve' => function( $source ) {
							//@codingStandardsIgnoreLine.
							return ! empty( $source->validFrom ) ? $source->validFrom : null;
						},
					),
				),
			)
		);

		register_graphql_field(
			'EventLinkedData',
			'offers',
			array(
				'type'        => array( 'list_of' => 'TicketLinkedData' ),
				'description' => __( 'Event tickets JSON-LD objects', 'ql-events' ),
				'resolve'     => function( $source ) {
					return ! empty( $source->offers ) ? $source->offers : null;
				},
			)
		);
	}
}
