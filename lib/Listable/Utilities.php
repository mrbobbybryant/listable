<?php
namespace Listable;

trait Utilities {
	public function isAssociative( $array ) {
		if ( is_object( $array ) ) {
			return false;
		}

		if ( is_array( $array ) ) {
			foreach ( $array as $key => $value ) {
				if ( is_string( $key ) ) {
					return true;
				}
			}
		}

		return false;
	}

	public function isMultidemensional( $array ) {
		foreach ( $array as $arr ) {
			if ( is_array( $arr ) ) {
				return true;
			}
		}
		return false;
	}

	public function containsObjects( $array ) {
		foreach ( $array as $arr ) {
			if ( is_object( $arr ) ) {
				return true;
			}
		}
		return false;
	}
}