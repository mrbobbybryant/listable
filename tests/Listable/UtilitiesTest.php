<?php
class UtilitiesTest extends \PHPUnit_Framework_TestCase {
	public function testUtilitiesIsMultidemensional() {
		$testArray = [
			[ 'name'    =>  'Bobby' ],
			[ 1, 2, 3 ]
		];
		$my_listable = new \Listable\Listable();

		$result = $my_listable->isMultidemensional( $testArray );
		$this->assertTrue( $result );
	}
}