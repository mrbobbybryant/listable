<?php
class LoopsTest extends \PHPUnit_Framework_TestCase {
	function testListableMap() {
		$testArray = [ 1, 2, 3 ];
		$expectedResult = [ 2, 3, 4 ];
		$my_listable = new \Listable\Listable();

		$result = $my_listable->_map( $testArray, function( $item ) {
			return $item + 1;
		} );

		$this->assertEquals($expectedResult, $result);
	}
}