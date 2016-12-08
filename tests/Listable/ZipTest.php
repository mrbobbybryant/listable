<?php
class ZipTest extends \PHPUnit_Framework_TestCase {
	public function testListableZip() {
		$testArray = [ 'yolo', 'bolo' ];
		$expectedResult = [
			[ 'yolo', 1, 'a' ],
			[ 'bolo', 2, 'b' ]
		];
		$my_listable = new \Listable\Listable( $testArray );

		$result = $my_listable->zip( [1,2], ['a','b'] )->toArray();
		$this->assertEquals($expectedResult, $result);
	}

	public function testListableZipWithoutArgs() {
		$testArray = [ 'yolo', 'bolo' ];
		$my_listable = new \Listable\Listable( $testArray );

		try {
			$my_listable->zip()->toArray();
		} catch (Exception $ex) {
			$this->assertEquals($ex->getMessage(), 'Zip expects at least one array as an argument.');
			return;
		}
	}

	public function testListableUnZip() {
		$testArray = [
			[ 'yolo', 1, 'a' ],
			[ 'bolo', 2, 'b' ]
		];
		$expectedResult = [
			[ 'yolo', 'bolo' ],
			[ 1, 2 ],
			[ 'a', 'b' ]
		];
		$my_listable = new \Listable\Listable( $testArray );

		$result = $my_listable->unzip()->toArray();
		$this->assertEquals($expectedResult, $result);
	}

	public function testListableUnZipWithNonMultiDimensionalArray() {
		$testArray = [
			[ 'yolo', 1, 'a' ],
			[ 'bolo', 2, 'b' ]
		];
		$expectedResult = [
			[ 'yolo', 'bolo' ],
			[ 1, 2 ],
			[ 'a', 'b' ]
		];
		$my_listable = new \Listable\Listable( $testArray );

		$result = $my_listable->unzip()->toArray();
		$this->assertEquals($expectedResult, $result);

	}

	public function testListableZipWith() {
		$expectedResult = [ 111,222 ];
		$my_listable = new \Listable\Listable( [ 1, 2 ] );

		$result = $my_listable->zipWith( function( $a, $b, $c ) {
			return $a + $b + $c;
		}, [ 10, 20 ], [ 100, 200 ] )->toArray();
		$this->assertEquals($expectedResult, $result);
	}
}