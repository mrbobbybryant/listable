<?php

namespace Listable;

use SebastianBergmann\CodeCoverage\Util;

class Listable {
	protected $items = [];

	/**
	 * Create a new list.
	 *
	 * @param  mixed  $items
	 * @return void
	 */
	public function __construct( $items = [] ) {
		$this->items = $this->format_list_items( $items );
	}

	protected function format_list_items( $items ) {

		if ( is_array( $items ) ) {
			return $items;
		} elseif ( $items instanceof self ) {
			return $items->all();
		} elseif ( $this->isJson( $items )) {
			return json_decode( $items, true );
		}

		return (array) $items;
	}

	protected function isJson( $string ) {
		json_decode($string);
		return ( json_last_error() == JSON_ERROR_NONE );
	}

	/**
	 * Get the collection of items as a plain array.
	 *
	 * @return array
	 */
	public function toArray() {
		return $this->map( function ( $value ) {
			return $value;
		} );
	}

	/**
	 * Get all of the items in the collection.
	 *
	 * @return array
	 */
	public function all() {
		return $this->items;
	}

	/**
	 * Function returns the length of the array.
	 *
	 * Function simply calls count on the array stored in $this->items.
	 * @return int
	 */
	public function length() {
		return count( $this->items );
	}

	/**
	 * Function returns the array within the listable as a JSON String.
	 * @return mixed|string|void
	 */
	public function toJSON() {
		return json_encode( $this->items );
	}

	public function merge( $new_items ) {
		$this->items = array_merge( $this->items, $new_items );
		return $this;
	}

	/**
	 * Function allows you to filter a listable to a subset of data.
	 *
	 * Function Iterates over all the elements within the listable,
	 * updating the listable's content to an array of all elements the predicate returns truthy for.
	 * @param callable $callback The method to apply to each element in the array.
	 *
	 * @return $this Returns an instance of the listable. This allows for method chaining.
	 */
	public function filter( $callback ) {
		$this->items = Loops::filter( $this->items, $callback );
		return $this;
	}

	/**
	 * Function allows you to loop over and modify each element in a listable.
	 *
	 * Function Iterates over all the elements within the listable,
	 * applying the user provided callback to each item in the listable.
	 * @param callable $callback The method to apply to each element in the array.
	 *
	 * @return $this Returns an instance of the listable. This allows for method chaining.
	 */
	public function map( $callback ) {
		$this->items = Loops::map( $this->items, $callback );
		return $this;
	}

	public function reduce( $callback, $default = null ) {
		return Loops::reduce( $this->items, $callback, $default );
	}

	/**
	 * Function converts a multidimensional array into a standard single array.
	 *
	 * @return $this Returns an instance of the listable. This allows for method chaining.
	 */
	public function flatten() {
		$this->items = Loops::flatten( $this->items );
		return $this;
	}

	/**
	 * Function flattens an array and then loops over the new array's elements.
	 *
	 * Function converts a multidimensional array into a standard single array, and then
	 * allows you to pass it a callback function to be applied to each item in the new
	 * flattened array.
	 *
	 * @param callable $callback The method to apply to each element in the array.
	 *
	 * @return $this Returns an instance of the listable. This allows for method chaining.
	 */
	public function flatMap( $callback ) {
		$flat = Loops::flatten( $this->items );
		$this->items = Loops::map( $flat, $callback );
		return $this;
	}

	/**
	 * Function filters an array and returns only the value you want.
	 * @param string $key The key/name of the value you want to pull out.
	 * @param null $default (optional) Useful for error handling when no items are found.
	 *
	 * @return $this Returns an instance of the listable. This allows for method chaining.
	 */
	public function pluck( $key, $default = null ) {
		$this->items = Loops::map( $this->items, function( $item ) use ( $key ) {
			if ( is_array( $item ) && array_key_exists( $key, $item ) ) {
				return $item[ $key ];
			}

			if ( is_object( $item )  && property_exists( $item, $key ) ) {
				return $item->$key;
			}
		});
		return $this;
	}

	/**
	 * Function filters an array and returns only the value you want.
	 *
	 * Similar to pluck except this function will take an array of keys. This lets you pick out
	 * multiple values from the listable's internal array.
	 * @param array $keys An array of keys to search for.
	 * @param null $default (optional) Useful for error handling when no items are found.
	 *
	 * @return $this Returns an instance of the listable. This allows for method chaining.
	 */
	public function pick( $keys, $default = null ) {
		if ( ! Utilities::isMultidemensional( $this->items ) && ! Utilities::containsObjects( $this->items ) ) {
			$temp = array_filter( Loops::map( $keys, function($key) {
				if ( array_key_exists( $key, $this->items ) ) {
					return $this->items[$key];
				}
			} ) );

			if ( ! empty( $temp ) ) {
				return $temp;
			}

			if ( ! empty( $default ) ) {
				return $default;
			}
		} else {
			$this->items = Loops::map( $this->items, function( $item ) use($keys) {
				if ( is_object( $item ) ) {
					$temp = new \stdClass();
					Loops::each( $keys, function( $key ) use ( $item, $temp ) {
						if ( property_exists( $item, $key ) ) {
							$temp->{$key} = $item->$key;
						}
					} );

					return $temp;
				}

				if ( is_array( $item ) ) {
					return array_filter( Loops::map( $keys, function($key) use($item) {
						if ( is_array( $item ) && array_key_exists( $key, $item ) ) {
							return $item[$key];
						}
					} ) );
				}

			} );
		}

		if ( ! empty( $default ) ) {
			return $default;
		}

		return $this;
	}

	public function get( $key, $default = null ) {

		if ( array_key_exists( $key, $this->items ) ) {
			return $this->items[ $key ];
		}

		return $default;
	}

	public function contains( $search, $default = null ) {

		if ( is_callable( $search ) ) {
			$temp = $this->filter( $search )->all();
		} else {
			$temp = $this->filter( function( $item ) use ( $search ) {
				return $search === $item;
			} )->all();
		}

		if ( ! empty( $temp ) ) {
			return true;
		} else if ( ! empty( $default ) ) {
			return $default;
		}

		return false;
	}

	public function sum() {
		return Loops::reduce( $this->items, function( $prev, $next ) {
			return $prev + $next;
		}, 0 );
	}

	public function first( $callback = null, $default = null ) {
		if ( 0 === $this->length() && is_null( $default ) ) {
			return [];
		}

		if ( 0 < $this->length() && is_null( $callback ) ) {
			return $this->items[0];
		}

		if ( 0 < $this->length() && is_callable( $callback ) ) {
			$temp = $this->filter( $callback )->all();
			if ( ! empty( $temp ) ) {
				return $temp[0];
			} else if ( ! empty( $default ) ) {
				return $default;
			} else {
				return [];
			}
		}

		if ( 0 === $this->length() && ! is_null( $default ) ) {
			return $default;
		}

		return [];
	}

	public function groupBy( $iterator, $key = null ) {
		$this->items = Loops::reduce( $this->items, function( $prev, $next, $index, $arr ) use ( $iterator, $key ) {
			if ( Utilities::isAssociative( $next ) && ! is_null( $key ) ) {
				$result = $this->groupByAssocArray( $next, $iterator, $key );

				if ( ! is_null( $result ) ) {
					$prev[$result][] = $next;
				}

				return $prev;
			}

			if ( is_object( $next ) && ! is_null( $key ) ) {
				$result = $this->groupByObjectKey( $next, $iterator, $key );

				if ( ! is_null( $result ) ) {
					$prev[$result][] = $next;
				}

				return $prev;
			}
			if ( ! Utilities::isAssociative( $arr ) && ! Utilities::isMultidemensional( $arr ) ) {
				$result = $this->groupByStandardArray( $next, $iterator );

				if ( ! is_null( $result ) ) {
					$prev[$result][] = $next;
				}

				return $prev;
			}
			return $prev;

		}, [] );

		return $this;

	}

	protected function groupByStandardArray( $array, $iterator ) {
		if ( is_callable( $iterator ) ) {
			return $iterator( $array );
		} else {
			return null;
		}

	}

	protected function groupByAssocArray( $array, $iterator, $key ) {
		if ( is_callable( $iterator  ) && array_key_exists( $key, $array ) ) {
			return $iterator( $array[$key] );
		} else {
			return null;
		}

	}

	protected function groupByObjectKey( $array, $iterator, $key ) {
		if ( is_callable( $iterator ) && property_exists( $array, $key ) ) {
			return $iterator( $array->$key );
		} else {
			return null;
		}

	}
}