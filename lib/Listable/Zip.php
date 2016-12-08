<?php
namespace Listable;

trait Zip {
	protected function _zip() {
		$length = count( func_get_args() );

		if ( 0 === $length ) {
			throw new \Exception( 'Zip expects at least one array as an argument.' );
		}

		$args = array_merge( [ $this->__value ], func_get_args() );
		return $this->_reduce( $args, function( $prev, $next ) {
			for( $i = 0; $i < count( $next ); $i++ ){
				$prev[$i][] = $next[$i];
			}
			return $prev;
		}, [] );
	}

	protected function _unzip() {
		if ( ! $this->isMultidemensional( $this->__value ) ) {
			throw new \Exception( 'Unzip can only be called on a multidimensional array.' );
		}

		return $this->_reduce( $this->__value, function( $prev, $next ) {
			for( $i = 0; $i < count( $next ); $i++ ){
				$prev[$i][] = $next[$i];
			}
			return $prev;
		}, [] );
	}

	protected function _zipWith( $iterator ) {
		$length = count( func_get_args() );

		if ( ! is_callable( $iterator ) ) {
			throw new \InvalidArgumentException( 'zipWith expects the first argument to be a valid callback function.' );
		}

		if ( 2 > $length ) {
			throw new \InvalidArgumentException( 'zipWith expects at least one array as an argument.' );
		}

		$args = array_merge( [ $this->__value ], array_slice( func_get_args(), 1, $length ) );
		$items = $this->_reduce( $args, function( $prev, $next ) {
			for( $i = 0; $i < count( $next ); $i++ ){
				$prev[$i][] = $next[$i];
			}
			return $prev;
		}, [] );

		return $this->_map( $items, function( $item ) use ( $iterator ) {
			return call_user_func_array( $iterator, $item );
		} );
	}
}