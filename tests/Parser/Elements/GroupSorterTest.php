<?php

namespace ApiGen\Tests\Parser\Elements;

use ApiGen\Configuration\Configuration;
use ApiGen\Configuration\ConfigurationOptions as CO;
use ApiGen\Parser\Elements\Elements;
use ApiGen\Parser\Elements\GroupSorter;
use ApiGen\Tests\MethodInvoker;
use Mockery;
use PHPUnit_Framework_Assert;
use PHPUnit_Framework_TestCase;


class GroupSorterTest extends PHPUnit_Framework_TestCase
{

	/**
	 * @var GroupSorter
	 */
	private $groupSorter;


	protected function setUp()
	{
		$configurationMock = Mockery::mock(Configuration::class);
		$configurationMock->shouldReceive('getOption')->with(CO::MAIN)->andReturn('One');
		$this->groupSorter = new GroupSorter(new Elements, $configurationMock);
	}


	public function testSort()
	{
		$groups = ['OneGroup' => [], 'OtherGroup' => [], 'OneMoreGroup' => []];
		$sortedGroups = $this->groupSorter->sort($groups);
		$this->assertCount(3, $sortedGroups);
		$this->assertArrayHasKey('OneGroup', $sortedGroups);
		$this->assertArrayHasKey('OtherGroup', $sortedGroups);
		$this->assertArrayHasKey('OneMoreGroup', $sortedGroups);
	}


	public function testSortNoneGroupOnly()
	{
		$groups = ['None' => []];
		$sortedGroups = $this->groupSorter->sort($groups);
		$this->assertSame([], $sortedGroups);
	}


	public function testIsNoneGroupOnly()
	{
		$groups['None'] = TRUE;
		$this->assertTrue(MethodInvoker::callMethodOnObject($this->groupSorter, 'isNoneGroupOnly', [$groups]));

		$groups['Packages'] = TRUE;
		$this->assertFalse(MethodInvoker::callMethodOnObject($this->groupSorter, 'isNoneGroupOnly', [$groups]));
	}


	public function testConvertGroupNamesToLower()
	{
		$groupNames = ['Some Group', 'Some other group'];
		$convertedGroupNames = MethodInvoker::callMethodOnObject(
			$this->groupSorter, 'convertGroupNamesToLower', [$groupNames]
		);
		$this->assertSame(['some group' => 0, 'some other group' => 1], $convertedGroupNames);
	}


	public function testAddMissingParentGroup()
	{
		$this->assertNull(PHPUnit_Framework_Assert::getObjectAttribute($this->groupSorter, 'groups'));
		MethodInvoker::callMethodOnObject($this->groupSorter, 'addMissingParentGroups', ['Some\Group\Name']);

		$groups = PHPUnit_Framework_Assert::getObjectAttribute($this->groupSorter, 'groups');
		$this->assertArrayHasKey('Some\Group\Name', $groups);
		$this->assertArrayHasKey('Some\Group', $groups);
		$this->assertArrayHasKey('Some', $groups);
	}


	public function testAddMissingElementTypes()
	{
		MethodInvoker::callMethodOnObject($this->groupSorter, 'addMissingElementTypes', ['Some\Group']);
		$groups = PHPUnit_Framework_Assert::getObjectAttribute($this->groupSorter, 'groups');
		$this->assertArrayHasKey('Some\Group', $groups);

		$someGroup = $groups['Some\Group'];
		$this->assertArrayHasKey('classes', $someGroup);
		$this->assertArrayHasKey('constants', $someGroup);
		$this->assertArrayHasKey('exceptions', $someGroup);
		$this->assertArrayHasKey('functions', $someGroup);
		$this->assertArrayHasKey('interfaces', $someGroup);
		$this->assertArrayHasKey('traits', $someGroup);
	}


	/**
	 * @dataProvider getCompareGroupsData()
	 */
	public function testCompareGroups($one, $two, $main, $expected)
	{
		$this->assertSame(
			$expected,
			MethodInvoker::callMethodOnObject($this->groupSorter, 'compareGroups', [$one, $two, $main])
		);
	}


	/**
	 * @return array[]
	 */
	public function getCompareGroupsData()
	{
		return [
			['GroupOne', 'OtherGroup', NULL, -8],
			['One', 'Two', NULL, -5],
			['One', 'Two', 'On', -1],
			['One', 'Two', 'Tw', 1],
		];
	}

}
