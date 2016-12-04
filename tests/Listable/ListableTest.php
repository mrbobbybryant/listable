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

	public function testListableFlatmap() {
		$expectedResult = [ 2, 2, 3, 4, 5];
		$my_listable = new \Listable\Listable([ 1, [ 1,2, [3,4]] ]);

		$result = $my_listable->flatMap( function( $item ) {
			return $item + 1;
		} )->all();
		$this->assertEquals($expectedResult, $result);
	}

	public function testListablePluckWithArray() {
		$testArray = [
			[ 'bar' => 'yolo', 'another' => 'boo' ],
			[ 'bar' => 'grrr', 'something' => 'test' ]
		];
		$expectedResult = [ 'yolo', 'grrr' ];
		$my_listable = new \Listable\Listable( $testArray );

		$result = $my_listable->pluck( 'bar' )->all();
		$this->assertEquals($expectedResult, $result);
	}

	public function testListablePluckWithObjects() {
		$obj1 = new stdClass();
		$obj1->bar = 'yolo';
		$obj1->another = 'boo';

		$obj2 = new stdClass();
		$obj2->bar = 'grrr';
		$obj2->something = 'test';
		$testArray = [ $obj1, $obj2 ];

		$expectedResult = [ 'yolo', 'grrr' ];
		$my_listable = new \Listable\Listable( $testArray );

		$result = $my_listable->pluck( 'bar' )->all();
		$this->assertEquals($expectedResult, $result);
	}

	public function testListableGetWithDefault() {
		$expectedResult = 'yolo';
		$my_listable = new \Listable\Listable([ 'bar' => 'yolo', 'baz' => 'grrr' ]);

		$result = $my_listable->get( 'bar' );
		$this->assertEquals($expectedResult, $result);
	}

	public function testListableSumWithNumbers() {
		$expectedResult = 9;
		$my_listable = new \Listable\Listable([ 2, 4, 3 ]);

		$result = $my_listable->sum();
		$this->assertEquals($expectedResult, $result);
	}

	public function testListablePickWithMultidemensionalArray() {
		$testArray = [
				[ 'bar' => 'yolo', 'another' => 'boo' ],
				[ 'bar' => 'grrr', 'something' => 'test' ]
		];
		$expectedResult = [ ['yolo'], [ 'grrr', 'test'] ];
		$my_listable = new \Listable\Listable( $testArray );

		$result = $my_listable->pick( [ 'bar', 'something' ] )->all();
		$this->assertEquals($expectedResult, $result);
	}

	public function testListablePickWithObject() {
		$obj1 = new stdClass();
		$obj1->bar = 'yolo';
		$obj1->another = 'boo';

		$obj2 = new stdClass();
		$obj2->bar = 'grrr';
		$obj2->something = 'test';
		$testArray = [ $obj1, $obj2 ];

		$obj3 = new stdClass();
		$obj3->bar = 'grrr';
		$obj3->something = 'test';

		$obj4 = new stdClass();
		$obj4->bar = 'yolo';

		$expectedResult = [ $obj4, $obj3 ];
		$my_listable = new \Listable\Listable( $testArray );

		$result = $my_listable->pick( [ 'bar', 'something' ] )->all();
		$this->assertEquals($expectedResult, $result);
	}

	public function testListablePickWithStandardArray() {
		$testArray = [
			'bar' => 'yolo',
			'another' => 'boo'
		];
		$expectedResult = [ 'yolo' ];
		$my_listable = new \Listable\Listable( $testArray );

		$result = $my_listable->pick( [ 'bar', 'something' ] );
		$this->assertEquals($expectedResult, $result);
	}

	public function testListableGroupByStandardArray() {
		$testArray = [ 4.2, 6.1, 6.4 ];
		$expectedResult = [ 4 => [ 4.2 ], 6 => [ 6.1, 6.4 ] ];
		$my_listable = new \Listable\Listable( $testArray );

		$result = $my_listable->groupBy( 'floor' )->all();
		$this->assertEquals($expectedResult, $result);
	}

	public function testListableGroupByAssocArrays() {
		$testArray = [
			[ 'team' => 'A', 'score' => 91 ],
			[ 'team' => 'B', 'score' => 86 ],
			[ 'team' => 'C', 'score' => 86 ]
		];
		$expectedResult = [
			91 => [ 0 => [ 'team' => 'A', 'score' => 91 ] ],
			86 => [
				0 => [ 'team' => 'B', 'score' => 86 ],
				1 => [ 'team' => 'C', 'score' => 86 ]
			]
		];
		$my_listable = new \Listable\Listable( $testArray );

		$result = $my_listable->groupBy( 'floor', 'score' )->all();
		$this->assertEquals($expectedResult, $result);
	}

	public function testListableGroupByArrayObjects() {
		$team1 = new stdClass();
		$team1->name = 'A';
		$team1->score = 91;

		$team2 = new stdClass();
		$team2->name = 'B';
		$team2->score = 86;

		$team3 = new stdClass();
		$team3->name = 'C';
		$team3->score = 86;

		$testArray = [ $team1, $team2, $team3 ];
		$expectedResult = [
			91 => [ 0 => $team1 ],
			86 => [
				0 => $team2,
				1 => $team3
			]
		];
		$my_listable = new \Listable\Listable( $testArray );

		$result = $my_listable->groupBy( 'floor', 'score' )->all();
		$this->assertEquals($expectedResult, $result);
	}

	public function testListableZip() {
		$testArray = [ 'yolo', 'bolo' ];
		$expectedResult = [
				[ 'yolo', 1, 'a' ],
				[ 'bolo', 2, 'b' ]
		];
		$my_listable = new \Listable\Listable( $testArray );

		$result = $my_listable->zip( [1,2], ['a','b'] )->all();
		$this->assertEquals($expectedResult, $result);
	}

	public function testListableZipWithoutArgs() {
		$testArray = [ 'yolo', 'bolo' ];
		$expectedResult = [ 'yolo', 'bolo' ];
		$my_listable = new \Listable\Listable( $testArray );

		$result = $my_listable->zip()->all();
		$this->assertEquals($expectedResult, $result);
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

		$result = $my_listable->unzip()->all();
		$this->assertEquals($expectedResult, $result);
	}

	public function testListableUnZipWitoutArgs() {
		$testArray = [ 'yolo', 'bolo' ];
		$expectedResult = [ 'yolo', 'bolo' ];
		$my_listable = new \Listable\Listable( $testArray );

		$result = $my_listable->unzip()->all();
		$this->assertEquals($expectedResult, $result);
	}

}