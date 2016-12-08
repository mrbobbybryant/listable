<?php
class DropTest extends \PHPUnit_Framework_TestCase {
	public function testListableDrop() {
		$testArray = [ 1, 2, 4, 3, 5 ];
		$expectedResult = [ 4, 3, 5 ];
		$my_listable = new \Listable\Listable( $testArray );

		$result = $my_listable->drop(2)->toArray();
		$this->assertEquals($expectedResult, $result);
	}

	public function testListableDropNoInput() {
		$testArray = [ 1, 2, 4, 3, 5 ];
		$expectedResult = [ 2, 4, 3, 5 ];
		$my_listable = new \Listable\Listable( $testArray );

		$result = $my_listable->drop()->toArray();
		$this->assertEquals($expectedResult, $result);
	}

	public function testListableDropTooBig() {
		$testArray = [ 1, 2, 4, 3, 5 ];
		$expectedResult = [ 1, 2, 4, 3, 5 ];
		$my_listable = new \Listable\Listable( $testArray );

		$result = $my_listable->drop(6)->toArray();
		$this->assertEquals($expectedResult, $result);
	}

	public function testListableDropRight() {
		$testArray = [ 1, 2, 4, 3, 5 ];
		$expectedResult = [ 1, 2, 4 ];
		$my_listable = new \Listable\Listable( $testArray );

		$result = $my_listable->dropRight(2)->toArray();
		$this->assertEquals($expectedResult, $result);
	}

	public function testListableDropRightNoInput() {
		$testArray = [ 1, 2, 4, 3, 5 ];
		$expectedResult = [ 1, 2, 4, 3 ];
		$my_listable = new \Listable\Listable( $testArray );

		$result = $my_listable->dropRight()->toArray();
		$this->assertEquals($expectedResult, $result);
	}

	public function testListableDropRightTooBig() {
		$testArray = [ 1, 2, 4, 3, 5 ];
		$expectedResult = [ 1, 2, 4, 3, 5 ];
		$my_listable = new \Listable\Listable( $testArray );

		$result = $my_listable->dropRight(6)->toArray();
		$this->assertEquals($expectedResult, $result);
	}

	public function testListableDropRightWhile() {
		$testArray = [ 1, 2, 4, 3, 5 ];
		$expectedResult = [ 1, 2 ];
		$my_listable = new \Listable\Listable( $testArray );

		$result = $my_listable->dropRightWhile( function( $item ) {
			return 4 > $item;
		} )->toArray();
		$this->assertEquals($expectedResult, $result);
	}

	public function testListableDropRightWhileNoResults() {
		$testArray = [ 1, 2, 4, 3, 5 ];
		$expectedResult = [ 1, 2, 4, 3, 5 ];
		$my_listable = new \Listable\Listable( $testArray );

		$result = $my_listable->dropRightWhile( function( $item ) {
			return 0 === $item;
		} )->toArray();
		$this->assertEquals($expectedResult, $result);
	}

	public function testListableDropRightWhileInvalidArguments() {
		$testArray = [ 1, 2, 4, 3, 5 ];
		$my_listable = new \Listable\Listable( $testArray );

		try {
			$my_listable->dropRightWhile( 'foeidh' )->toArray();
		} catch (Exception $ex) {
			$this->assertEquals($ex->getMessage(), 'dropWhile expects the provided argument to be a valid callback function.');
			return;
		}

	}

	public function testListableDropWhile() {
		$testArray = [ 1, 2, 4, 3, 5 ];
		$expectedResult = [ 4, 3, 5 ];
		$my_listable = new \Listable\Listable( $testArray );

		$result = $my_listable->dropWhile( function( $item ) {
			return 4 > $item;
		} )->toArray();
		$this->assertEquals($expectedResult, $result);
	}

	public function testListableDropWhileNoResults() {
		$testArray = [ 1, 2, 4, 3, 5 ];
		$expectedResult = [ 1, 2, 4, 3, 5 ];
		$my_listable = new \Listable\Listable( $testArray );

		$result = $my_listable->dropRightWhile( function( $item ) {
			return 0 === $item;
		} )->toArray();
		$this->assertEquals($expectedResult, $result);
	}
}