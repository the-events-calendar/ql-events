<?php
/**
 * GraphQL Object Type - OffersLinkedData
 *
 * @package WPGraphQL\TEC\Tickets\Type\WPObject
 * @since 0.0.1
 */

namespace WPGraphQL\TEC\Tickets\Type\WPObject;

use WPGraphQL\TEC\Common\Type\WPObject\EventLinkedData;

/**
 * Class - OffersLinkedData
 */
class OffersLinkedData {

	/**
	 * Name of the type.
	 *
	 * @var string Type name.
	 */
	public static $type = 'OffersLinkedData';
	/**
	 * {@inheritDoc}
	 */
	public static function register_type() : void {
		register_graphql_object_type(
			self::$type,
			[
				'description' => __( 'Offer JSON-LD data', 'wp-graphql-tec' ),
				'fields'      => [
					'availability'  => [
						'type'    => 'String',
						'resolve' => function( $source ) : ?string {
							return ! empty( $source->availability ) ? $source->availability : null;
						},
					],
					'category'      => [
						'type'    => 'String',
						'resolve' => function( $source ) : ?string {
							return ! empty( $source->category ) ? $source->category : null;
						},
					],
					'price'         => [
						'type'    => 'String',
						'resolve' => function( $source ) : ?string {
							return ! empty( $source->price ) ? $source->price : null;
						},
					],
					'priceCurrency' => [
						'type'    => 'String',
						'resolve' => function( $source ) : ?string {
							return ! empty( $source->priceCurrency ) ? $source->priceCurrency : null;
						},
					],
					'type'          => [
						'type'    => 'String',
						'resolve' => function( $source ) : ?string {
							return ! empty( $source->{'@type'} ) ? $source->{'@type'} : null;
						},
					],
					'url'           => [
						'type'    => 'String',
						'resolve' => function( $source ) : ?string {
							return ! empty( $source->url ) ? $source->url : null;
						},
					],
					'validFrom'     => [
						'type'    => 'String',
						'resolve' => function( $source ) : ?string {
							return ! empty( $source->validFrom ) ? $source->validFrom : null;
						},
					],
				],
			]
		);

		register_graphql_fields(
			EventLinkedData::$type,
			[
				'offers' => [
					'type'    => [ 'list_of' => self::$type ],
					'resolve' => function( $source ) : ?array {
						return ! empty( $source->offers ) ? (
							! is_array( $source->offers ) ? [ $source->offers ] : $source->offers
							) : null;
					},
				],
			],
		);
	}
}
