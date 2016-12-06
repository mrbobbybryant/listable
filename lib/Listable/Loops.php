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

	public static function flatten( $items, $depth ) {
		return self::reduce( $items, function ( $result, $item ) use ( $depth ) {

			if ( ! is_array( $item ) ) {
				return array_merge( $result, [ $item ] );
			} elseif ( $depth === 1 ) {
				return array_merge( $result, array_values( $item ) );
			} else {
				return array_merge( $result, self::flatten( $item, $depth - 1 ) );
			}

		}, []);
	}

	public static function each( $items, $callback ) {
		foreach( $items as $key => $item ) {
			$callback( $item, $key );
		}
	}

}