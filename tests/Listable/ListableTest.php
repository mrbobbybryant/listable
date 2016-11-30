<?php

class ListableTest extends \PHPUnit_Framework_TestCase
{
	public function testListableLengthCorrect() {
		$expectedResult = 3;
		$my_listable = new \Listable\Listable([1,2,3]);

		$result = $my_listable->length();
		$this->assertEquals($expectedResult, $result);
	}

	public function testListableLengthAssociativeArrays() {
		$expectedResult = 2;
		$my_listable = new \Listable\Listable( [ 'foo', 'bar' ], [ 'bar', 'baz' ] );

		$result = $my_listable->length();
		$this->assertEquals($expectedResult, $result);
	}

	public function testListableFirstNoInputs() {
		$expectedResult = 'foo';
		$my_listable = new \Listable\Listable(['foo', 'bar']);

		$result = $my_listable->first();
		$this->assertEquals($expectedResult, $result);
	}

	public function testListableFirstWithCallback() {
		$expectedResult = 'bar';
		$my_listable = new \Listable\Listable(['foo', 'bar']);

		$result = $my_listable->first( function( $item ) {
			return $item === 'bar';
		} );
		$this->assertEquals($expectedResult, $result);
	}

	public function testListableFirstWithDefault() {
		$expectedResult = ['foo', 'bar'];
		$my_listable = new \Listable\Listable([]);

		$result = $my_listable->first( null, ['foo', 'bar'] );
		$this->assertEquals($expectedResult, $result);
	}

	public function testListableFirstWithCallbackAndDefault() {
		$expectedResult = ['foo', 'bar'];
		$my_listable = new \Listable\Listable([]);

		$result = $my_listable->first( function( $item ) {
			return $item === 'bar';
		}, ['foo', 'bar'] );
		$this->assertEquals($expectedResult, $result);
	}

	public function testListableFirstWithoutCallbackOrDefault() {
		$expectedResult = [];
		$my_listable = new \Listable\Listable([]);

		$result = $my_listable->first();
		$this->assertEquals($expectedResult, $result);
	}

	public function testListableContainsWithoutDefault() {
		$expectedResult = true;
		$my_listable = new \Listable\Listable([1, 2, 3]);

		$result = $my_listable->contains(3);
		$this->assertEquals($expectedResult, $result);
	}

	public function testListableContainsWithDefault() {
		$expectedResult = 'yolo';
		$my_listable = new \Listable\Listable([1, 2, 3]);

		$result = $my_listable->contains(4, 'yolo');
		$this->assertEquals($expectedResult, $result);
	}

	public function testListableContainsWithCallableAndDefault() {
		$expectedResult = 'yolo';
		$my_listable = new \Listable\Listable([1, 2, 3]);

		$result = $my_listable->contains( function($item) {
			return $item > 4;
		}, 'yolo');
		$this->assertEquals($expectedResult, $result);
	}

	public function testListableContainsWithCallable() {
		$expectedResult = false;
		$my_listable = new \Listable\Listable([1, 2, 3]);

		$result = $my_listable->contains(function($item) {
			return $item > 4;
		});
		$this->assertEquals($expectedResult, $result);
	}

	public function testListableToJSON() {
		$expectedResult = '[1,2,3]';
		$my_listable = new \Listable\Listable([1, 2, 3]);

		$result = $my_listable->toJSON();
		$this->assertEquals($expectedResult, $result);
	}

	public function testListableFilter() {
		$expectedResult = [ 2, 4 ];
		$my_listable = new \Listable\Listable([1, 2, 3, 4]);

		$result = $my_listable->filter( function( $item ) {
			return $item % 2 === 0;
		} )->all();
		$this->assertEquals($expectedResult, $result);
	}

	public function testListableFilterNothingFound() {
		$expectedResult = [];
		$my_listable = new \Listable\Listable([1, 5, 3, 7]);

		$result = $my_listable->filter( function( $item ) {
			return $item % 2 === 0;
		} )->all();
		$this->assertEquals($expectedResult, $result);
	}

	public function testListableFilterEmptyList() {
		$expectedResult = [];
		$my_listable = new \Listable\Listable([]);

		$result = $my_listable->filter( function( $item ) {
			return $item % 2 === 0;
		} )->all();
		$this->assertEquals($expectedResult, $result);
	}

	public function testListableMap() {
		$expectedResult = [ 2, 3,4 ];
		$my_listable = new \Listable\Listable([ 1, 2, 3 ]);

		$result = $my_listable->map( function( $item ) {
			return $item + 1;
		} )->all();
		$this->assertEquals($expectedResult, $result);
	}

	public function testListableMapEmptyList() {
		$expectedResult = [];
		$my_listable = new \Listable\Listable([]);

		$result = $my_listable->map( function( $item ) {
			return $item + 1;
		} )->all();
		$this->assertEquals($expectedResult, $result);
	}

	public function testListableReduce() {
		$expectedResult = 9;
		$my_listable = new \Listable\Listable([ 1, 5, 3 ]);

		$result = $my_listable->reduce( function( $prev, $next ) {
			return $prev + $next;
		} );
		$this->assertEquals($expectedResult, $result);
	}

	public function testListableReduceEmptyList() {
		$expectedResult = 0;
		$my_listable = new \Listable\Listable([]);

		$result = $my_listable->reduce( function( $prev, $next ) {
			return $prev + $next;
		} );
		$this->assertEquals($expectedResult, $result);
	}

	public function testListableReduceWithDefault() {
		$expectedResult = 109;
		$my_listable = new \Listable\Listable([ 1, 5, 3 ]);

		$result = $my_listable->reduce( function( $prev, $next ) {
			return $prev + $next;
		}, 100 );
		$this->assertEquals($expectedResult, $result);
	}

	public function testListableFlatten() {
		$expectedResult = [ 1, 2, 3, 4 ];
		$my_listable = new \Listable\Listable([ [ 1, 2 ], [ 3, 4 ] ]);

		$result = $my_listable->flatten()->all();
		$this->assertEquals($expectedResult, $result);
	}

	public function testListableFlattenComplex() {
		$expectedResult = [ 1, 2, 3, 4, 'foo', 'bar', 'baz' ];
		$my_listable = new \Listable\Listable([ [ 1, 2 ], [ 3, 4 ], [ 'foo', [ 'bar', 'baz' ] ] ]);

		$result = $my_listable->flatten()->all();
		$this->assertEquals($expectedResult, $result);
	}

	public function testListableGetWithoutDefault() {
		$expectedResult = 'yolo';
		$my_listable = new \Listable\Listable([ 'bar' => 'yolo', 'baz' => 'grrr' ]);

		$result = $my_listable->get( 'bar' );
		$this->assertEquals($expectedResult, $result);
	}

	public function testListableGetWithDefault() {
		$expectedResult = 'something';
		$my_listable = new \Listable\Listable([ 'bar' => 'yolo', 'baz' => 'grrr' ]);

		$result = $my_listable->get( 'barr', 'something' );
		$this->assertEquals($expectedResult, $result);
	}
}