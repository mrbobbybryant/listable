<?php
namespace Listable\Helper;
/**
 * Create a list from the given value.
 *
 * @param  mixed  $value
 * @return new List
 */
function listable( $value = null ) {
	return new \Listable\Base\Listable( $value );
}