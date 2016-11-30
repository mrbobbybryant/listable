<?php

namespace Listable;

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

	public function length() {
		return count( $this->items );
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

	public function toJSON() {
		return json_encode( $this->items );
	}

	public function merge( $new_items ) {
		$this->items = array_merge( $this->items, $new_items );
		return $this;
	}

	public function filter( $callback ) {
		$this->items = Loops::filter( $this->items, $callback );
		return $this;
	}

	public function map( $callback ) {
		$this->items = Loops::map( $this->items, $callback );
		return $this;
	}

	public function reduce( $callback, $default = null ) {
		return Loops::reduce( $this->items, $callback, $default );
	}

	public function flatten() {
		$this->items = Loops::flatten( $this->items );
		return $this;
	}

	public function flatMap( $callback ) {
		$flat = Loops::flatten( $this->items );
		$this->items = Loops::map( $flat, $callback );
		return $this;
	}

	public function pluck( $key, $default = null ) {
		$this->items = Loops::map( $this->items, function( $item ) use ( $key ) {
			if ( array_key_exists( $key, $item ) ) {
				return $item[ $key ];
			}
		});
		return $this;
	}

	public function pick( $keys, $default = null ) {
		$this->items = Loops::map( $this->items, function( $item ) use($keys) {
			return array_filter( Loops::map( $keys, function($key) use($item) {
				if ( array_key_exists( $key, $item ) ) {
					return $item[$key];
				}

			} ) );
		} );

		return $this;
	}



	public function get( $key, $default = null ) {

		if ( array_key_exists( $key, $this->items ) ) {
			return $this->items[ $key ];
		}

		return $default;
	}

	public function sum() {
		return Loops::reduce( $this->items, function( $prev, $next ) {
			return $prev + $next;
		}, 0 );
	}
}