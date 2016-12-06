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
			return $items->toArray();
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
	 * Get all of the items in the collection.
	 *
	 * @return array
	 */
	public function toArray() {
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
	public function flatten( $depth = 0 ) {
		$this->items = Loops::flatten( $this->items, $depth );
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
		$flat = Loops::flatten( $this->items, $depth = 0 );
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
			$temp = $this->filter( $search )->toArray();
		} else {
			$temp = $this->filter( function( $item ) use ( $search ) {
				return $search === $item;
			} )->toArray();
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
			$temp = $this->filter( $callback )->toArray();
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

	/**
	 * Function allows you to group items, array, and object based in user selected criteria.
	 *
	 * This function will work with standard array, multidimensional arrays, and array of
	 * objects. Items will be grouped by applying the iterator to each item in an array, or
	 * by applying the iterator to the specified key when working with multidimensional arrays and
	 * arrays of objects.
	 *
	 * @param callable $iterator The function used to determine how items should be grouped.
	 * @param null $key (optional) Allows you to define which key the iterator should be applied to.
	 *
	 * @return $this Returns an instance of the listable. This allows for method chaining.
	 */
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

	/**
	 * Function will combine multiple arrays into two new arrays. Accepts an unimted number of
	 * arrays to be zipped up.
	 *
	 * Function will combine all items from each index for all input arrays into a new array.
	 * for example [1,2] and ['a', 'b'] would become [ [1,'a'], [2,'b'] ]
	 *
	 * @return $this Returns an instance of the listable. This allows for method chaining.
	 */
	public function zip() {

		if ( 0 < count( func_get_args() ) ) {
			$args = array_merge( [ $this->items ], func_get_args() );
			$this->items = Loops::reduce( $args, function( $prev, $next ) {
				for( $i = 0; $i < count( $next ); $i++ ){
					$prev[$i][] = $next[$i];
				}
				return $prev;
			}, [] );
		}

		return $this;
	}

	/**
	 * Function does the same thing a zip but in the reverse order. So it will unzip two input arrays
	 * into multiple new array.
	 *
	 * For example [ [1,'a', true], [2,'b', false] ] will become [ [1,2], ['a','b'], [true, false] ]
	 *
	 * @return $this Returns an instance of the listable. This allows for method chaining.
	 */
	public function unzip() {

		if ( Utilities::isMultidemensional( $this->items ) ) {
			$this->items = Loops::reduce( $this->items, function( $prev, $next ) {
				for( $i = 0; $i < count( $next ); $i++ ){
					$prev[$i][] = $next[$i];
				}
				return $prev;
			}, [] );
		}

		return $this;
	}

	public function zipWith( $iterator, $args ) {
		$args = array_merge( [ $this->items ], array_slice( func_get_args(), 1, count(func_get_args() ) ) );
		$items = Loops::reduce( $args, function( $prev, $next ) {
			for( $i = 0; $i < count( $next ); $i++ ){
				$prev[$i][] = $next[$i];
			}
			return $prev;
		}, [] );

		$this->items = Loops::map( $items, function( $item ) use ( $iterator ) {
			return $iterator( $item );
		} );
		return $this;
	}

	/**
	 * Function creates an array of elements split into groups the length of size.
	 *
	 * If array can't be split evenly,the final chunk will be the remaining elements.
	 * If the size is 0 then this function will simply return the original array unchanged.
	 *
	 * @param int $size Defaults to zero.
	 *
	 * @return $this Returns an instance of the listable. This allows for method chaining.
	 */
	public function chunk( $size = 0 ) {

		if ( 0 < $size ) {
			$result = new \stdClass();
			$result->items = [];
			$result->pointer = 0;
			$results = Loops::reduce( $this->items, function( $prev, $next ) use ( $size ) {
				$prev->items[ $prev->pointer ][] = $next;
				if ( $size === count( $prev->items[ $prev->pointer ] ) ) {
					$prev->pointer++;
				}
				return $prev;
			}, $result );

			$this->items = $results->items;
		}

		return $this;
	}

	/**
	 * Creates an array with all falsey values removed.
	 *
	 * Falsey values that this function removes are 0, false, '' (empty string), and null.
	 *
	 * @return $this Returns an instance of the listable. This allows for method chaining.
	 */
	public function compact() {
		$this->items = array_values( array_filter( $this->items ) );
		Return $this;
	}

	/**
	 * Function returns the difference between the Listable's internal array and an unlimited
	 * number of input arrays.
	 *
	 * For example, if the Listable contains [ 1, 2 ] and we pass difference [1, 3] and [1, 5].
	 * This function would return [2], since the number 2 is not found inside any of the input
	 * arrays. This function does not mutate the Listable's array. For that take a look at pull().
	 *
	 * @return array An array of all the differences.
	 */
	public function difference() {
		$arrays = func_get_args();
		$results = $this->compareArrayItems( $arrays, $this->items, function( $item, $array ) {
			return ! in_array( $item, $array );
		} );

		$results = new self( $results );

		return array_unique( $results->flatten()->toArray() );

	}

	/**
	 * Function returns the similarities between the Listable's internal array and an unlimited
	 * number of input arrays.
	 *
	 * For example, if the Listable contains [ 1, 2 ] and we pass difference [1, 3] and [1, 5].
	 * This function would return [1], since the number 1 is found inside every arrays. This function
	 * does not mutate the Listable's array.
	 *
	 * @return array An array of all the differences.
	 */
	public function intersection() {
		$arrays = func_get_args();

		$results = $this->compareArrayItems( $arrays, $this->items, function( $item, $array ) {
			return in_array( $item, $array );
		} );

		$results = new self( $results );

		return array_unique( $results->flatten()->toArray() );
	}

	/**
	 * Function allows you to compare the items between two input array. It uses an iterator
	 * make the comparison.
	 *
	 * The iterator function receives an array from the multidimensional array, and one item from
	 * the second input array($items).
	 *
	 * @param array $arrays An array of array. Multidimensional array.
	 * @param array $items The second array to compare
	 * @param callable $iterator The function used to compare items in both arrays.
	 *
	 * @return int
	 */
	protected function compareArrayItems( $arrays, $items, $iterator ) {
		return Loops::reduce( $arrays, function( $prev, $array ) use( $items, $iterator ) {
			$prev[] = Loops::filter( $items, function( $item ) use ( $array, $iterator ) {
				if ( $iterator( $item, $array ) ) {
					return $item;
				}
			} );

			return $prev;

		}, []);
	}

	/**
	 * Function creates a slice of array with n elements dropped from the beginning.
	 *
	 * @param int $size The number of elements to drop from the beginning of the array.
	 *
	 * @return $this Returns an instance of the listable. This allows for method chaining.
	 */
	public function drop( $size = 1 ) {
		if ( $size <= count( $this->items ) ) {
			$this->items = array_slice( $this->items, $size, count( $this->items ) );
		}

		return $this;
	}

	/**
	 * Function creates a slice of array with n elements dropped from the beginning.
	 *
	 * @param int $size The number of elements to drop from the end of the array.
	 *
	 * @return $this Returns an instance of the listable. This allows for method chaining.
	 */
	public function dropRight( $size = 1 ) {
		$length = count( $this->items );

		if ( $size <= $length ) {
			$size = $length - $size;
			$slice = $length * -1;;
			$this->items = array_slice( $this->items, $slice, $size );
		}

		return $this;
	}

	/**
	 * Function creates a slice of array excluding elements dropped from the beginning until
	 * the iterator returns false. The iterator is passed the $iterator and the index of
	 * current item.
	 *
	 * @param callable $iterator The function to test each value again. Must return true or false.
	 *
	 * @return $this Returns an instance of the listable. This allows for method chaining.
	 */
	public function dropWhile( $iterator ) {
		$results = Loops::map( $this->items, $iterator );
		$index = array_search( false, $results );
		$this->drop( $index );
		return $this;
	}

	/**
	 * Function creates a slice of array excluding elements dropped from the end until
	 * the iterator returns false. The iterator is passed the $iterator and the index of
	 * current item.
	 *
	 * @param callable $iterator The function to test each value again. Must return true or false.
	 *
	 * @return $this Returns an instance of the listable. This allows for method chaining.
	 */
	public function dropRightWhile( $iterator ) {
		$results = Loops::map( $this->items, $iterator );
		$index = array_search( false, $results );
		$slice = ( 0 === $index ) ? 0 : $index + 1;
		$this->dropRight( $slice );
		return $this;
	}

	/**
	 * Function removes all given values or keys from the Listablr's array.
	 *
	 * @param array $values An array of values to remove from array.
	 *
	 * This function can also accept an array of keys which can be used to remove items from
	 * multidimensional arrays.
	 *
	 * @return $this Returns an instance of the listable. This allows for method chaining.
	 */
	public function pull( $values ) {
		$this->items = Loops::map( $this->items, function( $item, $index ) use( $values ) {
			if ( ! Utilities::isAssociative( $item ) && ! is_object( $item ) ) {
				if ( in_array( $item, $values ) ) {
					return false;
				}
			} else if ( Utilities::isAssociative( $item ) ) {
				return array_diff_key( $item, array_flip( (array) $values ) );
			} else if ( is_object( $item ) ) {
				Loops::each( $values, function( $value ) use( $item ) {
					if ( property_exists( $item, $value ) ) {
						unset( $item->$value);
					}
				} );
			}

			return $item;
		} );

		$this->items = array_values( array_filter( $this->items ) );
		return $this;
	}

}