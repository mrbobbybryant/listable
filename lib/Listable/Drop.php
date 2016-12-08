<?php
namespace Listable;

trait Drop {
	/**
	 * Function creates a slice of array with n elements dropped from the beginning.
	 *
	 * @param int $size The number of elements to drop from the beginning of the array.
	 *
	 * @return Listable This allows for method chaining.
	 */
	public function drop( $size = 1 ) {
		if ( $size <= $this->length() ) {
			return self::of( array_slice( $this->__value, $size, count( $this->__value ) ) );
		}

		return self::of( $this->__value );
	}

	/**
	 * Function creates a slice of array with n elements dropped from the beginning.
	 *
	 * @param int $size The number of elements to drop from the end of the array.
	 *
	 * @return $this Returns an instance of the listable. This allows for method chaining.
	 */
	public function dropRight( $size = 1 ) {
		$length = count( $this->__value );

		if ( $size <= $length ) {
			$size = $length - $size;
			$slice = $length * -1;;
			return self::of( array_slice( $this->__value, $slice, $size ) );
		}

		return self::of( $this->__value );
	}

	/**
	 * Function creates a slice of array excluding elements dropped from the beginning until
	 * the iterator returns false. The iterator is passed the $iterator and the index of
	 * current item.
	 *
	 * @param callable $iterator The function to test each value again. Must return true or false.
	 *
	 * @throws \InvalidArgumentException if the provided argument is not a callable function.
	 *
	 * @return Listable This allows for method chaining.
	 */
	public function dropWhile( $iterator ) {
		if ( ! is_callable( $iterator ) ) {
			throw new \InvalidArgumentException( 'dropWhile expects the provided argument to be a valid callback function. ' );
		}

		$results = $this->_map( $this->__value, $iterator );
		$index = array_search( false, $results );

		return $this->drop( $index );
	}

	/**
	 * Function creates a slice of array excluding elements dropped from the end until
	 * the iterator returns false. The iterator is passed the $iterator and the index of
	 * current item.
	 *
	 * @param callable $iterator The function to test each value again. Must return true or false.
	 *
	 * @throws \InvalidArgumentException if the provided argument is not a callable function.
	 *
	 * @return $this Returns an instance of the listable. This allows for method chaining.
	 */
	public function dropRightWhile( $iterator ) {

		if ( ! is_callable( $iterator ) ) {
			throw new \InvalidArgumentException( 'dropWhile expects the provided argument to be a valid callback function.' );
		}

		$results = $this->_map( $this->__value, $iterator );
		$index = array_search( false, $results );
		$slice = ( 0 === $index ) ? 0 : $index + 1;

		return $this->dropRight( $slice );
	}
}