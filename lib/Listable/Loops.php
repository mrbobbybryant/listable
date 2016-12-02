<?php
namespace Listable;

class Loops {
	public static function map( $items, $callback ) {
		$results = [];
		$index = 0;

		foreach( $items as $item ) {
			$results[] = $callback( $item, $index );
			$index++;
		}

		return $results;
	}

	public static function filter( $items, $callback ) {
		$results = [];
		$index = 0;

		foreach( $items as $item ) {
			$result = $callback( $item, $index );

			if ( $result ) {
				$results[] = $item;
			}
		}

		return $results;
	}

	public static function reduce( $items, $callback, $default ) {
		$prev = ( is_null( $default ) ) ? 0 : $default;
		$index = 0;

		foreach( $items as $item ) {
			$prev = $callback( $prev, $item, $index, $items );
		}

		return $prev;
	}

	public static function flatten( $items ) {
		return is_array( $items ) ?
		array_reduce( $items, function ( $prev, $next ) {
			return array_merge( $prev, self::flatten( $next ) );
		},[] ) : [ $items ];
	}

	public static function each( $items, $callback ) {
		foreach( $items as $key => $item ) {
			$callback( $item, $key );
		}
	}

}