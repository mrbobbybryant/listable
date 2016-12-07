<?php

class ListableTest extends \PHPUnit_Framework_TestCase
{
	public function testListableCreationWithJSON() {
		$obj1 = new stdClass();
		$obj1->name = 'Bobby';
		$obj1->age = 28;
		$obj1->location = 'USA';
		$obj1->member = true;

		$obj2 = new stdClass();
		$obj2->name = 'Lucy';
		$obj2->age = 28;
		$obj2->location = 'Canada';
		$obj2->member = false;

		$expectedResult = [ $obj1, $obj2 ];

		$input = "[{\"name\":\"Bobby\",\"age\":28,\"location\":\"USA\",\"member\":true},{\"name\":\"Lucy\",\"age\":28,\"location\":\"Canada\",\"member\":false}]";
		$my_listable = new \Listable\Listable($input);

		$this->assertEquals($expectedResult, $my_listable->toArray());

	}

	public function testListableCreationWithJSONConvertObjects() {
		$obj1 = new stdClass();
		$obj1->name = 'Bobby';
		$obj1->age = 28;
		$obj1->location = 'USA';
		$obj1->member = true;

		$obj2 = new stdClass();
		$obj2->name = 'Lucy';
		$obj2->age = 28;
		$obj2->location = 'Canada';
		$obj2->member = false;

		$expectedResult = [
			[
				'name'      =>  'Bobby',
				'age'       =>  28,
				'location'  =>  'USA',
				'member'    =>  true
			],
			[
				'name'      =>  'Lucy',
				'age'       =>  28,
				'location'  =>  'Canada',
				'member'    =>  false
			]
		];

		$input = "[{\"name\":\"Bobby\",\"age\":28,\"location\":\"USA\",\"member\":true},{\"name\":\"Lucy\",\"age\":28,\"location\":\"Canada\",\"member\":false}]";
		$my_listable = new \Listable\Listable($input, true);

		$this->assertEquals($expectedResult, $my_listable->toArray());

	}

	public function testListableCreationObjectInput() {
		$obj1 = new stdClass();
		$obj1->name = 'Bobby';
		$obj1->age = 28;
		$obj1->location = 'USA';
		$obj1->member = true;

		$expectedResult = [ $obj1 ];

		$my_listable = new \Listable\Listable( $obj1, false );

		$this->assertEquals($expectedResult, $my_listable->toArray());

	}

	public function testListableCreationObjectInputConvertToArray() {
		$obj1 = new stdClass();
		$obj1->name = 'Bobby';
		$obj1->age = 28;
		$obj1->location = 'USA';
		$obj1->member = true;

		$expectedResult = [
			'name'      =>  'Bobby',
			'age'       =>  28,
			'location'  =>  'USA',
			'member'    =>  true
		];

		$my_listable = new \Listable\Listable( $obj1, true );

		$this->assertEquals($expectedResult, $my_listable->toArray());

	}

	public function testListableCreationArrayInput() {

		$expectedResult = [ 1,2,3 ];

		$my_listable = new \Listable\Listable( $expectedResult );

		$this->assertEquals($expectedResult, $my_listable->toArray());

	}

	public function testListableCreationAnotherListableAsInput() {

		$expectedResult = [ 1,2,3 ];

		$another_listable = new \Listable\Listable( $expectedResult );
		$my_listable = new \Listable\Listable( $another_listable );

		$this->assertEquals($expectedResult, $my_listable->toArray());

	}

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
		} )->toArray();
		$this->assertEquals($expectedResult, $result);
	}

	public function testListableFilterNothingFound() {
		$expectedResult = [];
		$my_listable = new \Listable\Listable([1, 5, 3, 7]);

		$result = $my_listable->filter( function( $item ) {
			return $item % 2 === 0;
		} )->toArray();
		$this->assertEquals($expectedResult, $result);
	}

	public function testListableFilterEmptyList() {
		$expectedResult = [];
		$my_listable = new \Listable\Listable([]);

		$result = $my_listable->filter( function( $item ) {
			return $item % 2 === 0;
		} )->toArray();
		$this->assertEquals($expectedResult, $result);
	}

	public function testListableMap() {
		$expectedResult = [ 2, 3,4 ];
		$my_listable = new \Listable\Listable([ 1, 2, 3 ]);

		$result = $my_listable->map( function( $item ) {
			return $item + 1;
		} )->toArray();
		$this->assertEquals($expectedResult, $result);
	}

	public function testListableMapEmptyList() {
		$expectedResult = [];
		$my_listable = new \Listable\Listable([]);

		$result = $my_listable->map( function( $item ) {
			return $item + 1;
		} )->toArray();
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

		$result = $my_listable->flatten()->toArray();
		$this->assertEquals($expectedResult, $result);
	}

	public function testListableFlattenComplex() {
		$expectedResult = [ 1, 2, 3, 4, 'foo', 'bar', 'baz' ];
		$my_listable = new \Listable\Listable([ [ 1, 2 ], [ 3, 4 ], [ 'foo', [ 'bar', 'baz' ] ] ]);

		$result = $my_listable->flatten()->toArray();
		$this->assertEquals($expectedResult, $result);
	}

	public function testListableFlattenOneLevel() {
		$testArray = [
			[
				[ 'name' => 'Bobby', 'age' => 27 ]
			],
			[
				[ 'name' => 'John', 'age' => 29 ]

			]
		];

		$expectedResult = [
			[ 'name' => 'Bobby', 'age' => 27 ],
			[ 'name' => 'John', 'age' => 29 ]
		];
		$my_listable = new \Listable\Listable($testArray);

		$result = $my_listable->flatten(1)->toArray();
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
		} )->toArray();
		$this->assertEquals($expectedResult, $result);
	}

	public function testListablePluckWithArray() {
		$testArray = [
			[ 'bar' => 'yolo', 'another' => 'boo' ],
			[ 'bar' => 'grrr', 'something' => 'test' ]
		];
		$expectedResult = [ 'yolo', 'grrr' ];
		$my_listable = new \Listable\Listable( $testArray );

		$result = $my_listable->pluck( 'bar' )->toArray();
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

		$result = $my_listable->pluck( 'bar' )->toArray();
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

		$result = $my_listable->pick( [ 'bar', 'something' ] )->toArray();
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

		$result = $my_listable->pick( [ 'bar', 'something' ] )->toArray();
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

		$result = $my_listable->groupBy( 'floor' )->toArray();
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

		$result = $my_listable->groupBy( 'floor', 'score' )->toArray();
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

		$result = $my_listable->groupBy( 'floor', 'score' )->toArray();
		$this->assertEquals($expectedResult, $result);
	}

	public function testListableGroupByArrayObjectsKeyNotFound() {
		$team1 = new stdClass();
		$team1->name = 'A';
		$team1->score = 91;

		$team2 = new stdClass();
		$team2->name = 'B';
		$team2->score = 86;

		$testArray = [ $team1, $team2 ];
		$my_listable = new \Listable\Listable( $testArray );

		try {
			$my_listable->groupBy( 'floor', 'base' )->toArray();
		} catch (Exception $ex) {
			$this->assertEquals($ex->getMessage(), 'The key provide to groupBy is not a valid object property.');
			return;
		}
	}

	public function testListableGroupByWithInvalidFunction() {
		$testArray = [ 4.2, 6.1, 6.4 ];
		$my_listable = new \Listable\Listable( $testArray );

		try {
			$my_listable->groupBy( 'floorred' )->toArray();
		} catch (Exception $ex) {
			$this->assertEquals($ex->getMessage(), 'Function expects the callback to be a callable function.');
			return;
		}
	}

	public function testListableGroupByAssocArraysWithInvalidKey() {
		$testArray = [
				[ 'team' => 'A', 'score' => 91 ],
				[ 'team' => 'B', 'score' => 86 ],
				[ 'team' => 'C', 'score' => 86 ]
		];

		$my_listable = new \Listable\Listable( $testArray );

		try {
			$my_listable->groupBy( 'floor', 'base' )->toArray();
		} catch (Exception $ex) {
			$this->assertEquals($ex->getMessage(), 'The key provide does not exist in the current collection.');
			return;
		}
	}

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
		$expectedResult = [ 'yolo', 'bolo' ];
		$my_listable = new \Listable\Listable( $testArray );

		$result = $my_listable->zip()->toArray();
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

		$result = $my_listable->unzip()->toArray();
		$this->assertEquals($expectedResult, $result);
	}

	public function testListableUnZipWitoutArgs() {
		$testArray = [ 'yolo', 'bolo' ];
		$expectedResult = [ 'yolo', 'bolo' ];
		$my_listable = new \Listable\Listable( $testArray );

		$result = $my_listable->unzip()->toArray();
		$this->assertEquals($expectedResult, $result);
	}

	public function testListableChunkSizeTwo() {
		$testArray = [ 'yolo', 'bolo', 'foo', 'bar', 'baz' ];
		$expectedResult = [
			['yolo', 'bolo'],
			[ 'foo', 'bar' ],
			[ 'baz' ]
		];
		$my_listable = new \Listable\Listable( $testArray );

		$result = $my_listable->chunk(2)->toArray();
		$this->assertEquals($expectedResult, $result);
	}

	public function testListableChunkSizeThree() {
		$testArray = [ 'yolo', 'bolo', 'foo', 'bar', 'baz' ];
		$expectedResult = [
				['yolo', 'bolo', 'foo'],
				[ 'bar', 'baz' ]
		];
		$my_listable = new \Listable\Listable( $testArray );

		$result = $my_listable->chunk(3)->toArray();
		$this->assertEquals($expectedResult, $result);
	}

	public function testListableChunkSizeZero() {
		$testArray = [ 'yolo', 'bolo', 'foo', 'bar', 'baz' ];
		$expectedResult = [ 'yolo', 'bolo', 'foo', 'bar', 'baz' ];
		$my_listable = new \Listable\Listable( $testArray );

		$result = $my_listable->chunk(0)->toArray();
		$this->assertEquals($expectedResult, $result);
	}

	public function testListableCompactWithFalseItems() {
		$testArray = [0, 1, false, 2, '', 3, null];
		$expectedResult = [ 1, 2, 3 ];
		$my_listable = new \Listable\Listable( $testArray );

		$result = $my_listable->compact()->toArray();
		$this->assertEquals($expectedResult, $result);
	}

	public function testListableCompactWithoutFalseItems() {
		$testArray = [ 'foo', 1, 'bar', 2, 3 ];
		$expectedResult = [ 'foo', 1, 'bar', 2, 3 ];
		$my_listable = new \Listable\Listable( $testArray );

		$result = $my_listable->compact()->toArray();
		$this->assertEquals($expectedResult, $result);
	}

	public function testListableDifferenceWithOneArray() {
		$testArray = [ 1, 2 ];
		$expectedResult = [ 2 ];
		$my_listable = new \Listable\Listable( $testArray );

		$result = $my_listable->difference([ 1, 3 ]);
		$this->assertEquals($expectedResult, $result);
	}

	public function testListableDifferenceWithManyArray() {
		$testArray = [ 1, 2 ];
		$expectedResult = [ 2 ];
		$my_listable = new \Listable\Listable( $testArray );

		$result = $my_listable->difference([ 1, 3 ], [ 1, 4 ], [ 1, 5 ]);
		$this->assertEquals($expectedResult, $result);
	}

	public function testListableDifferenceWithStringValues() {
		$testArray = [ 'foo', 'bar' ];
		$expectedResult = [ 'bar' ];
		$my_listable = new \Listable\Listable( $testArray );

		$result = $my_listable->difference([ 'foo', 'baz' ]);
		$this->assertEquals($expectedResult, $result);
	}

	public function testListableIntersection() {
		$testArray = [ 1, 2 ];
		$expectedResult = [ 1 ];
		$my_listable = new \Listable\Listable( $testArray );

		$result = $my_listable->intersection([ 1, 3 ]);
		$this->assertEquals($expectedResult, $result);
	}

	public function testListableIntersectionWithManyArray() {
		$testArray = [ 1, 2 ];
		$expectedResult = [ 1 ];
		$my_listable = new \Listable\Listable( $testArray );

		$result = $my_listable->intersection([ 1, 3 ], [ 1, 4 ], [ 1, 5 ]);
		$this->assertEquals($expectedResult, $result);
	}

	public function testListableIntersectioneWithStringValues() {
		$testArray = [ 'foo', 'bar' ];
		$expectedResult = [ 'foo' ];
		$my_listable = new \Listable\Listable( $testArray );

		$result = $my_listable->intersection([ 'foo', 'baz' ]);
		$this->assertEquals($expectedResult, $result);
	}

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

	public function testListablePull() {
		$testArray = [ 2, 3, 2, 1, 4 ];
		$expectedResult = [ 3, 4 ];
		$my_listable = new \Listable\Listable( $testArray );

		$result = $my_listable->pull( [ 2, 1 ] )->toArray();
		$this->assertEquals($expectedResult, $result);
	}

	public function testListablePullWithAssocArray() {
		$testArray = [
			[ 'name' => 'John', 'age' => 28 ],
			[ 'name' => 'Lucy', 'age' => 26 ]
		];
		$expectedResult = [
			[ 'name' => 'John' ],
			[ 'name' => 'Lucy' ]
		];
		$my_listable = new \Listable\Listable( $testArray );

		$result = $my_listable->pull( [ 'age' ] )->toArray();
		$this->assertEquals($expectedResult, $result);
	}

	public function testListablePullWithObjects() {
		$team1 = new stdClass();
		$team1->name = 'A';
		$team1->score = 91;
		$team1->points = 203;

		$team2 = new stdClass();
		$team2->name = 'B';
		$team2->score = 86;
		$team2->points = 203;

		$team3 = new stdClass();
		$team3->name = 'A';

		$team4 = new stdClass();
		$team4->name = 'B';

		$testArray = [ $team1, $team2 ];
		$expectedResult = [ $team3, $team4 ];
		$my_listable = new \Listable\Listable( $testArray );

		$result = $my_listable->pull( [ 'score', 'points' ] )->toArray();
		$this->assertEquals($expectedResult, $result);
	}

}