<?php

namespace Listable;

use SebastianBergmann\CodeCoverage\Util;
use Symfony\Component\Config\Definition\Exception\Exception;

class Listable {

	use Utilities;
	use Zip;
	use Drop;
	use Loops;

	protected $__value;

	/**
	 * Create a new list.
	 *
	 * @param  mixed  $items
	 * @return void
	 */
	public function __construct( $items = [], $objects = false ) {
		$this->__value = $this->format_list_items( $items, $objects );
	}

	/**
	 * A static constructor to creating new Listable Instances.
	 *
	 * This function is used internally, and externally to set the internal value properties value. This function
	 * accepts Arrays, Objects, and JSON,
	 *
	 * @param mixed $x Then data to convert to a listable.
	 *
	 * @return Listable
	 */
	public static function of( $x ) {
		return new self( $x );
	}

	/**
	 * Internal function used by the constructor to format and parse the input data correctly before setting it
	 * as the Listable's value.
	 *
	 * @param mixed $items Items to insert into Listable wrapper.
	 * @param bool $objects Useful when inserting Objects and JSON. If set to true then the Object and JSON Objects will
	 * be converted to Associative arrays. By default Object and JSON Object remain objects.
	 *
	 * @return array|mixed|object
	 */
	protected function format_list_items( $items, $objects ) {

		if ( is_array( $items ) ) {
			return $items;
		} elseif ( $items instanceof self ) {
			return $items->toArray();
		} else if ( is_object( $items ) ) {
			if ( $objects ) {
				return (array) $items;
			} else {
				return [ $items ];
			}
		} elseif ( $this->isJson( $items )) {
			return json_decode( $items, $objects );
		}

		return (array) $items;
	}

	/**
	 * Function checks to see if a Listable input value is JSON. This is used by the constructor to determine who
	 * to handle certain input data types.
	 *
	 * @param mixed $string The data submitted to the constructor.
	 *
	 * @return bool True is $string is found to be JSON. Otherwise it returns false.
	 */
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
		return $this->__value;
	}

	/**
	 * Function returns the length of the array.
	 *
	 * Function simply calls count on the array stored in $this->__value.
	 * @return int
	 */
	public function length() {
		return count( $this->__value );
	}

	/**
	 * Function returns the array within the listable as a JSON String.
	 * @return mixed|string|void
	 */
	public function toJSON() {
		return json_encode( $this->__value );
	}

	/**
	 * Function will merge an input array with the existing data found in $this->__value
	 * @param array $new_items
	 *
	 * @return Listable This allows for method chaining.
	 */
	public function merge( $new_items ) {
		return self::of( array_merge( $this->__value, $new_items ) );
	}

	/**
	 * Function allows you to filter a listable to a subset of data.
	 *
	 * Function Iterates over all the elements within the listable,
	 * updating the listable's content to an array of all elements the predicate returns truthy for.
	 * @param callable $callback The method to apply to each element in the array.
	 *
	 * @return Listable This allows for method chaining.
	 */
	public function filter( $callback ) {
		return self::of( $this->_filter( $this->__value, $callback ) );
	}

	/**
	 * Function allows you to loop over and modify each element in a listable.
	 *
	 * Function Iterates over all the elements within the listable,
	 * applying the user provided callback to each item in the listable.
	 * @param callable $callback The method to apply to each element in the array.
	 *
	 * @return Listable This allows for method chaining.
	 */
	public function map( $callback ) {
		return self::of( $this->_map( $this->__value, $callback ) );
	}

	public function reduce( $callback, $default = null ) {
		return $this->_reduce( $this->__value, $callback, $default );
	}

	/**
	 * Function converts a multidimensional array into a standard single array.
	 *
	 * @return $this Returns an instance of the listable. This allows for method chaining.
	 */
	public function flatten( $depth = 0 ) {
		return self::of( $this->_flatten( $this->__value, $depth ) );
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
		$flat = $this->_flatten( $this->__value, $depth = 0 );
		return self::of( $this->_map( $flat, $callback ) );
	}

	/**
	 * Function filters an array and returns only the value you want.
	 * @param string $key The key/name of the value you want to pull out.
	 * @param null $default (optional) Useful for error handling when no items are found.
	 *
	 * @return $this Returns an instance of the listable. This allows for method chaining.
	 */
	public function pluck( $key, $default = null ) {
		return self::of( $this->_map( $this->__value, function( $item ) use ( $key ) {
			if ( is_array( $item ) && array_key_exists( $key, $item ) ) {
				return $item[ $key ];
			}

			if ( is_object( $item )  && property_exists( $item, $key ) ) {
				return $item->$key;
			}
		} ) );
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
		if ( ! $this->isMultidemensional( $this->__value ) && ! $this->containsObjects( $this->__value ) ) {
			$temp = array_filter( $this->_map( $keys, function( $key ) {
				if ( array_key_exists( $key, $this->__value ) ) {
					return $this->__value[$key];
				}
			} ) );

			if ( ! empty( $temp ) ) {
				return $temp;
			}

			if ( ! empty( $default ) ) {
				return $default;
			}
		} else {
			$this->__value = $this->_map( $this->__value, function( $item ) use($keys) {
				if ( is_object( $item ) ) {
					$temp = new \stdClass();
					$this->each( $keys, function( $key ) use ( $item, $temp ) {
						if ( property_exists( $item, $key ) ) {
							$temp->{$key} = $item->$key;
						}
					} );

					return $temp;
				}

				if ( is_array( $item ) ) {
					return array_filter( $this->_map( $keys, function($key) use($item) {
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

		if ( array_key_exists( $key, $this->__value ) ) {
			return $this->__value[ $key ];
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
		return $this->_reduce( $this->__value, function( $prev, $next ) {
			return $prev + $next;
		}, 0 );
	}

	public function first( $callback = null, $default = null ) {
		if ( 0 === $this->length() && is_null( $default ) ) {
			return [];
		}

		if ( 0 < $this->length() && is_null( $callback ) ) {
			return $this->__value[0];
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
	 * @return Listable This allows for method chaining.
	 */
	public function groupBy( $callback, $key = null ) {
		if ( ! is_callable( $callback ) ) {
			throw new \Exception('Function expects the callback to be a callable function.');
		}

		return self::of( $this->_reduce( $this->toArray(), function( $prev, $next, $index, $arr ) use ( $callback, $key ) {
			if ( $this->isAssociative( $next ) && ! is_null( $key ) ) {
				$result = $this->groupByAssocArray( $next, $callback, $key );
				$prev[$result][] = $next;
				return $prev;
			}

			if ( is_object( $next ) && ! is_null( $key ) ) {
				$result = $this->groupByObjectKey( $next, $callback, $key );
				$prev[$result][] = $next;
				return $prev;
			}
			if ( ! $this->isAssociative( $arr ) && ! $this->isMultidemensional( $arr ) ) {
				$result = $this->groupByStandardArray( $next, $callback );
				$prev[$result][] = $next;
				return $prev;
			}
			return $prev;

		}, [] ) );

	}

	protected function groupByStandardArray( $array, $iterator ) {
		return $iterator( $array );
	}

	protected function groupByAssocArray( $array, $iterator, $key ) {
		if ( array_key_exists( $key, $array ) ) {
			return $iterator( $array[$key] );
		} else {
			throw new \Exception('The key provide does not exist in the current collection.');
		}

	}

	protected function groupByObjectKey( $array, $iterator, $key ) {
		if ( property_exists( $array, $key ) ) {
			return $iterator( $array->$key );
		} else {
			throw new \Exception('The key provide to groupBy is not a valid object property.');
		}

	}

	/**
	 * Function will combine multiple arrays into two new arrays. Accepts an unimted number of
	 * arrays to be zipped up.
	 *
	 * Function will combine all items from each index for all input arrays into a new array.
	 * for example [1,2] and ['a', 'b'] would become [ [1,'a'], [2,'b'] ]
	 *
	 * @return Listable This allows for method chaining.
	 */
	public function zip() {
		return self::of( call_user_func_array( [ $this, '_zip' ], func_get_args() ) );
	}

	/**
	 * Function does the same thing a zip but in the reverse order. So it will unzip two input arrays
	 * into multiple new array.
	 *
	 * For example [ [1,'a', true], [2,'b', false] ] will become [ [1,2], ['a','b'], [true, false] ]
	 *
	 * @return Listable This allows for method chaining.
	 */
	public function unzip() {
		return self::of( call_user_func_array( [ $this, '_unzip' ], func_get_args() ) );
	}

	/**
	 * Function will zip multiple arrays together, and uses the $iterator function to zip them with.
	 *
	 * @param callable $iterator The function
	 * @param array $args An unlimted number of arrays to zip. Function requires at least one.
	 *
	 * @throws \InvalidArgumentException if the first provided argument is not a callable function.
	 * @throws \InvalidArgumentException if the provided arguments does not contain at least one type 'array'
	 *
	 * @return Listable This allows for method chaining.
	 */
	public function zipWith( $iterator ) {
		return self::of( call_user_func_array( [ $this, '_zipwith' ], func_get_args() ) );
	}

	/**
	 * Function creates an array of elements split into groups the length of size.
	 *
	 * If array can't be split evenly,the final chunk will be the remaining elements.
	 * If the size is 0 then this function will simply return the original array unchanged.
	 *
	 * @param int $size Defaults to zero.
	 *
	 * @return Listable This allows for method chaining.
	 */
	public function chunk( $size = 0 ) {

		if ( 0 < $size ) {
			$result = new \stdClass();
			$result->items = [];
			$result->pointer = 0;
			$results = $this->_reduce( $this->__value, function( $prev, $next ) use ( $size ) {
				$prev->items[ $prev->pointer ][] = $next;
				if ( $size === count( $prev->items[ $prev->pointer ] ) ) {
					$prev->pointer++;
				}
				return $prev;
			}, $result );

			return self::of( $results->items );
		}

		return self::of( $this->__value );
	}

	/**
	 * Creates an array with all falsey values removed.
	 *
	 * Falsey values that this function removes are 0, false, '' (empty string), and null.
	 *
	 * @return Listable This allows for method chaining.
	 */
	public function compact() {
		return self::of( array_values( array_filter( $this->__value ) ) );
	}

	/**
	 * Function returns the difference between the Listable's internal array and an unlimited
	 * number of input arrays.
	 *
	 * For example, if the Listable contains [ 1, 2 ] and we pass difference [1, 3] and [1, 5].
	 * This function would return [2], since the number 2 is not found inside any of the input
	 * arrays. This function does not mutate the Listable's array. For that take a look at pull().
	 *
	 * @param mixed $arrays,... An unlimited number of additional array to compare. Requires at least one.
	 *
	 * @throws \InvalidArgumentException Function expects at least one array as an argument.
	 *
	 * @return array An array of all the differences.
	 */
	public function difference( $arrays ) {
		$arrays = func_get_args();

		if ( ! is_array( $arrays[0] ) ) {
			throw new \InvalidArgumentException( 'Difference expects at least one array as an argument.' );
		}

		$results = call_user_func_array( [ $this, 'compareArrayItems' ], [ $arrays, $this->__value, function( $item, $array ) {
			return ! in_array( $item, $array );
		} ] );


		return array_unique( $this->_flatten( $results, $depth = 0 ) );

	}

	/**
	 * Function returns the similarities between the Listable's internal array and an unlimited
	 * number of input arrays.
	 *
	 * For example, if the Listable contains [ 1, 2 ] and we pass difference [1, 3] and [1, 5].
	 * This function would return [1], since the number 1 is found inside every arrays. This function
	 * does not mutate the Listable's array.
	 *
	 * @param mixed $arrays,... An unlimited number of additional array to compare. Requires at least one.
	 *
	 * @throws \InvalidArgumentException Function expects at least one array as an argument.
	 *
	 * @return array An array of all the differences.
	 */
	public function intersection( $arrays ) {
		$arrays = func_get_args();

		if ( ! is_array( $arrays[0] ) ) {
			throw new \InvalidArgumentException( 'Intersection expects at least one array as an argument.' );
		}
		$results = call_user_func_array( [ $this, 'compareArrayItems' ], [ $arrays, $this->__value, function( $item, $array ) {
			return in_array( $item, $array );
		} ] );

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
		return $this->_reduce( $arrays, function( $prev, $array ) use( $items, $iterator ) {
			$prev[] = $this->_filter( $items, function( $item ) use ( $array, $iterator ) {
				if ( $iterator( $item, $array ) ) {
					return $item;
				}
			} );

			return $prev;

		}, []);
	}

	/**
	 * Function removes all given values or keys from the Listable's array.
	 *
	 * @param array $values An array of values to remove from array.
	 *
	 * This function can also accept an array of keys which can be used to remove items from
	 * multidimensional arrays.
	 *
	 * @throws \InvalidArgumentException if the provided argument is not of type array.
	 *
	 * @return Listable This allows for method chaining.
	 */
	public function pull( $values ) {

		if ( ! is_array( $values ) ) {
			throw new \InvalidArgumentException( 'Pull expects the provided argument to be of type array.' );
		}

		$this->__value = $this->_map( $this->__value, function( $item, $index ) use( $values ) {
			if ( ! $this->isAssociative( $item ) && ! is_object( $item ) ) {
				if ( in_array( $item, $values ) ) {
					return false;
				}
			} else if ( $this->isAssociative( $item ) ) {
				return array_diff_key( $item, array_flip( (array) $values ) );
			} else if ( is_object( $item ) ) {
				$this->each( $values, function( $value ) use( $item ) {
					if ( property_exists( $item, $value ) ) {
						unset( $item->$value);
					}
				} );
			}

			return $item;
		} );

		return self::of( array_values( array_filter( $this->__value ) ) );
	}

}