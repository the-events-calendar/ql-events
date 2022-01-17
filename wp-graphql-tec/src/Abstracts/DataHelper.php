<?php
/**
 * Abstract Data Helper.
 *
 * @package WPGraphQL\TEC\Abstracts;
 * @since 0.0.1
 */

namespace WPGraphQL\TEC\Abstracts;

use GraphQL\Deferred;
use GraphQL\Type\Definition\ResolveInfo;
use WPGraphQL\AppContext;
use WPGraphQL\Data\Connection\AbstractConnectionResolver;

use WPGraphQL\TEC\Utils\Utils;

/**
 * Abstract Class - DataHelper
 */
abstract class DataHelper {
	/**
	 * The helper name. E.g. `events` or `tickets`.
	 *
	 * @var string
	 */
	public static string $name;

	/**
	 * The GraphQL type. E.g. `Event` or `RsvpTicket`.
	 *
	 * @var string
	 */
	public static string $type;

	/**
	 * The WordPress type. E.g. `tribe_events` or `tec_tc_ticket`.
	 *
	 * @var string
	 */
	public static string $wp_type;

	/**
	 * The name of the DataLoader to use.
	 *
	 * @var string
	 */
	public static string $loader_name;

	/**
	 * Overwrites the default connection config for the data type.
	 *
	 * @param array $config .
	 */
	public static function get_connection_config( array $config ) : array {
		$config['connectionArgs'] = static::get_connection_args( $config['connectionArgs'] ?? [] );

		$post_type = Utils::graphql_type_to_post_type( $config['toType'] ) ?? $config['toType'];

		$config['resolve'] = static::get_connection_resolver( $post_type );

		/**
		 * Filters the connection config.
		 *
		 * @param array $config The connection config.
		 */
		return apply_filters( 'graphql_tec_' . static::$name . '_connection_config', $config );
	}

	/**
	 * Gets our (filtered) connection args.
	 *
	 * @param array $connection_args .
	 */
	public static function get_connection_args( array $connection_args ) : array {
		$connection_args = array_merge( $connection_args, static::connection_args() );

		return apply_filters( 'graphql_tec_' . static::$name . '_connection_args', $connection_args );
	}

	/**
	 * Wrapper for the ConnectionResolver class.
	 *
	 * @param mixed       $source    Source.
	 * @param array       $args      Query args to pass to the connection resolver.
	 * @param AppContext  $context   The context of the query to pass along.
	 * @param ResolveInfo $info      The ResolveInfo object.
	 * @param string      $post_type The post type.
	 */
	public static function resolve_connection( $source, array $args, AppContext $context, ResolveInfo $info, string $post_type = '' ) : Deferred {
		if ( empty( $post_type ) ) {
			$post_type = static::$wp_type;
		}

		$resolver_name = static::resolver();

		/** @var AbstractConnectionResolver $resolver */
		$resolver = new $resolver_name( $source, $args, $context, $info, $post_type );

		return $resolver->get_connection();
	}

	/**
	 * Returns the data object.
	 *
	 * @param int        $id      Object ID.
	 * @param AppContext $context AppContext object.
	 * @return Deferred|null
	 */
	public static function resolve_object( $id, AppContext $context ) : ?Deferred {
		if ( empty( $id ) ) {
			return null;
		}

		$context->get_loader( static::$loader_name )->load( $id );

		return new Deferred(
			function () use ( $id, $context ) {
				return $context->get_loader( static::$loader_name )->load( $id );
			}
		);
	}


	/**
	 * Gets the data-specific resolver callback.
	 *
	 * @param string $post_type The post type.
	 */
	/**
	 * {@inheritDoc}
	 */
	public static function get_connection_resolver( string $post_type ) : callable {
		return function ( $source, array $args, AppContext $context, ResolveInfo $info ) use ( $post_type ) {
			$args = self::get_processed_args( $args );

			if ( 'EventCategory' === $info->parentType->name ) {
				$args['where']['categoryId'] = $source->databaseId;
			}

			return static::resolve_connection( $source, $args, $context, $info, $post_type );
		};
	}

	/**
	 * Gets our (filtered) where args after they've been processed.
	 *
	 * @param array $args .
	 */
	public static function get_processed_args( array $args ) : array {
		if ( empty( $args['where'] ) ) {
			return $args;
		}

		$args['where'] = static::process_where_args( $args['where'] );

		return apply_filters( 'graphql_tec_' . static::$name . '_processed_args', $args );
	}

	/**
	 * Gets the Resolver class.
	 *
	 * @return string
	 */
	abstract public static function resolver() : string;

	/**
	 * Sets our data-specific connection args.
	 *
	 * This should be overwritten by the child class.
	 */
	public static function connection_args() : array {
		return [];
	}

	/**
	 * Sets the logic to transform our where args into a format the resolver can understand.
	 *
	 * This should be overwritten by the child class.
	 *
	 * @param array $args .
	 */
	public static function process_where_args( array $args ) : array {
		return $args;
	}


}
