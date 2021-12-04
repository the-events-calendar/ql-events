<?php
/**
 * GraphQL Object Type - CustomFields
 *
 * @package WPGraphQL\TEC\EventsPro\Type\WPObject
 * @since 0.0.1
 */

namespace WPGraphQL\TEC\EventsPro\Type\WPObject;

use WPGraphQL\TEC\Utils\Utils;
use WPGraphQL\Type\WPEnumType;

/**
 * Class - CustomFields
 */
class CustomFields {

	/**
	 * Name of the type.
	 *
	 * @var string Type name.
	 */
	public static $type = 'CustomFields';
	/**
	 * {@inheritDoc}
	 */
	public static function register_type() : void {
		register_graphql_object_type(
			self::$type,
			[
				'description' => __( 'Custom fields defined in The Events Calendar', 'wp-graphql-tec' ),
				'fields'      => [],
			],
		);

		$fields = tribe_get_option( 'custom-fields', '' );

		if ( empty( $fields ) ) {
			return;
		}

		foreach ( $fields as $field ) {
			$name = Utils::to_camel_case( $field['label'] ?? $field['name'] );

			if ( in_array( $field['type'], [ 'text', 'textarea', 'url' ], true ) ) {
				register_graphql_field(
					self::$type,
					$name,
					[
						'type'        => 'String',
						/* translators: custom field label */
						'description' => sprintf( __( 'The \"%s\" custom field', 'wp-graphql-tec' ), $field['label'] ),
						'resolve'     => fn( $source ) => $source[ $field['label'] ] ?? null,
					]
				);
			} elseif ( in_array( $field['type'], [ 'radio', 'checkbox', 'dropdown' ], true ) ) {
				register_graphql_enum_type(
					'EventCustomField' . $name . 'Enum',
					[
						/* translators: custom field label */
						'description' => sprintf( __( 'The possible values for the "%s" custom field.', 'wp-graphql-tec' ), $field['label'] ),
						'values'      => self::get_values( $field ),
					]
				);

				register_graphql_field(
					self::$type,
					$name,
					[
						'type'        => 'EventCustomField' . $name . 'Enum',
						/* translators: custom field label */
						'description' => sprintf( __( 'The \"%s\" custom field', 'wp-graphql-tec' ), $field['label'] ),
						'resolve'     => fn( $source ) => $source[ $field['label'] ] ?? null,
					]
				);
			}
		}
	}

		/**
		 * Programmatically generate the enum values for the custom field.
		 *
		 * @param array $field .
		 */
	public static function get_values( array $field ) : array {
		$options = explode( PHP_EOL, $field['values'] );
		foreach ( $options as $value ) {
			$values[ $value ] = [
				'name'        => WPEnumType::get_safe_name( rtrim( $value ) ),
				'value'       => $value,
				/* translators: GraphQL ticket type name */
				'description' => sprintf( __( 'A \"%s\" value', 'wp-graphql-tec' ), $value ),
			];
		}
		return $values;
	}
}
