<?php
namespace Listable;

trait Loops {

	/**
	 * Function allows you to loop over and modify each element in a listable.
	 *
	 * Function Iterates over all the elements within the listable,
	 * applying the user provided callback to each item in the listable.
	 * @param callable $callback The method to apply to each element in the array.
	 *
	 * @return array
	 */
	public function _map( $items, $callback ) {
		$results = [];
		$index = 0;

		foreach( $items as $item ) {
			$results[] = $callback( $item, $index );
			$index++;
		}

		return $results;
	}

	/**
	 * Function allows you to filter a listable to a subset of data.
	 *
	 * Function Iterates over all the elements within the listable,
	 * updating the listable's content to an array of all elements the predicate returns truthy for.
	 * @param callable $callback The method to apply to each element in the array.
	 *
	 * @return array
	 */
	public function _filter( $items, $callback ) {
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

	public function _reduce( $items, $callback, $default ) {
		$prev = ( is_null( $default ) ) ? 0 : $default;
		$index = 0;

		foreach( $items as $item ) {
			$prev = $callback( $prev, $item, $index, $items );
		}

		return $prev;
	}

	/**
	 * Function converts a multidimensional array into a standard single array.
	 *
	 * @return array
	 */
	public function _flatten( $items, $depth ) {
		return $this->_reduce( $items, function ( $result, $item ) use ( $depth ) {

			if ( ! is_array( $item ) ) {
				return array_merge( $result, [ $item ] );
			} elseif ( $depth === 1 ) {
				return array_merge( $result, array_values( $item ) );
			} else {
				return array_merge( $result, $this->_flatten( $item, $depth - 1 ) );
			}

		}, [] );
	}

	public function each( $items, $callback ) {
		foreach( $items as $key => $item ) {
			$callback( $item, $key );
		}
	}

}