<?php declare(strict_types=1);

namespace ApiGen\Parser\Tests\Elements;

use ApiGen\Contracts\Configuration\ConfigurationInterface;
use ApiGen\Parser\Elements\Elements;
use ApiGen\Parser\Elements\GroupSorter;
use ApiGen\Parser\Tests\MethodInvoker;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

final class GroupSorterTest extends TestCase
{
    /**
     * @var GroupSorter
     */
    private $groupSorter;


    protected function setUp(): void
    {
        $configurationMock = $this->createMock(ConfigurationInterface::class);
        $configurationMock->method('getMain')
            ->willReturn('');
        $this->groupSorter = new GroupSorter(new Elements, $configurationMock);
    }


    public function testSort(): void
    {
        $groups = ['OneGroup' => [], 'OtherGroup' => [], 'OneMoreGroup' => []];
        $sortedGroups = $this->groupSorter->sort($groups);
        $this->assertCount(3, $sortedGroups);
        $this->assertArrayHasKey('OneGroup', $sortedGroups);
        $this->assertArrayHasKey('OtherGroup', $sortedGroups);
        $this->assertArrayHasKey('OneMoreGroup', $sortedGroups);
    }


    public function testSortNoneGroupOnly(): void
    {
        $groups = ['None' => []];
        $sortedGroups = $this->groupSorter->sort($groups);
        $this->assertSame([], $sortedGroups);
    }


    public function testIsNoneGroupOnly(): void
    {
        $groups['None'] = true;
        $this->assertTrue(MethodInvoker::callMethodOnObject($this->groupSorter, 'isNoneGroupOnly', [$groups]));
    }


    public function testConvertGroupNamesToLower(): void
    {
        $groupNames = ['Some Group', 'Some other group'];
        $convertedGroupNames = MethodInvoker::callMethodOnObject(
            $this->groupSorter,
            'convertGroupNamesToLower',
            [$groupNames]
        );
        $this->assertSame(['some group' => 0, 'some other group' => 1], $convertedGroupNames);
    }


    public function testAddMissingParentGroup(): void
    {
        $this->assertNull(Assert::getObjectAttribute($this->groupSorter, 'groups'));
        MethodInvoker::callMethodOnObject($this->groupSorter, 'addMissingParentGroups', ['Some\Group\Name']);

        $groups = Assert::getObjectAttribute($this->groupSorter, 'groups');
        $this->assertArrayHasKey('Some\Group\Name', $groups);
        $this->assertArrayHasKey('Some\Group', $groups);
        $this->assertArrayHasKey('Some', $groups);
    }


    public function testAddMissingElementTypes(): void
    {
        MethodInvoker::callMethodOnObject($this->groupSorter, 'addMissingElementTypes', ['Some\Group']);
        $groups = Assert::getObjectAttribute($this->groupSorter, 'groups');
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
    public function testCompareGroups(string $one, string $two, string $main, int $expected): void
    {
        $this->assertSame(
            $expected,
            MethodInvoker::callMethodOnObject($this->groupSorter, 'compareGroups', [$one, $two, $main])
        );
    }


    /**
     * @return mixed[]
     */
    public function getCompareGroupsData(): array
    {
        return [
            ['GroupOne', 'OtherGroup', '', -8],
            ['One', 'Two', '', -5],
            ['One', 'Two', 'On', -1],
            ['One', 'Two', 'Tw', 1],
        ];
    }
}
