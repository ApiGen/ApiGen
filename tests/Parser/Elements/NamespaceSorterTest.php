<?php declare(strict_types=1);

namespace ApiGen\Parser\Tests\Elements;

use ApiGen\Contracts\Configuration\ConfigurationInterface;
use ApiGen\Contracts\Parser\Elements\NamespaceSorterInterface;
use ApiGen\Parser\Elements\Elements;
use ApiGen\Parser\Elements\NamespaceSorter;
use ApiGen\Tests\MethodInvoker;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

final class NamespaceSorterTest extends TestCase
{
    /**
     * @var NamespaceSorterInterface
     */
    private $namespaceSorter;

    protected function setUp(): void
    {
        $configurationMock = $this->createMock(ConfigurationInterface::class);

        $this->namespaceSorter = new NamespaceSorter(new Elements, $configurationMock);
    }

    public function testSort(): void
    {
        $groups = ['OneGroup' => [], 'OtherGroup' => [], 'OneMoreGroup' => []];
        $sortedGroups = $this->namespaceSorter->sort($groups);
        $this->assertCount(3, $sortedGroups);
        $this->assertArrayHasKey('OneGroup', $sortedGroups);
        $this->assertArrayHasKey('OtherGroup', $sortedGroups);
        $this->assertArrayHasKey('OneMoreGroup', $sortedGroups);
    }

    public function testSortNoneOnly(): void
    {
        $groups = ['None' => []];
        $sortedGroups = $this->namespaceSorter->sort($groups);
        $this->assertSame(['None' => []], $sortedGroups);
    }

    public function testIsNoneOnly(): void
    {
        $groups['None'] = true;
        $this->assertTrue(MethodInvoker::callMethodOnObject($this->namespaceSorter, 'isNoneOnly', [$groups]));
    }

    public function testAddMissingParentNamespaces(): void
    {
        $this->assertNull(Assert::getObjectAttribute($this->namespaceSorter, 'namespaces'));
        MethodInvoker::callMethodOnObject(
            $this->namespaceSorter, 'addMissingParentNamespaces', ['Some\Group\Name']
        );

        $groups = Assert::getObjectAttribute($this->namespaceSorter, 'namespaces');
        $this->assertArrayHasKey('Some\Group\Name', $groups);
        $this->assertArrayHasKey('Some\Group', $groups);
        $this->assertArrayHasKey('Some', $groups);
    }

    public function testAddMissingElementTypes(): void
    {
        MethodInvoker::callMethodOnObject($this->namespaceSorter, 'addMissingElementTypes', ['Some\Group']);
        $groups = Assert::getObjectAttribute($this->namespaceSorter, 'namespaces');
        $this->assertArrayHasKey('Some\Group', $groups);

        $someGroup = $groups['Some\Group'];
        $this->assertArrayHasKey('classes', $someGroup);
        $this->assertArrayHasKey('exceptions', $someGroup);
        $this->assertArrayHasKey('functions', $someGroup);
        $this->assertArrayHasKey('interfaces', $someGroup);
        $this->assertArrayHasKey('traits', $someGroup);
    }

    /**
     * @dataProvider getCompareNamespacesData()
     */
    public function testCompareNamespaces(string $one, string $two, int $expected): void
    {
        $this->assertSame(
            $expected,
            MethodInvoker::callMethodOnObject($this->namespaceSorter, 'compareNamespaceNames', [$one, $two])
        );
    }

    /**
     * @return mixed[]
     */
    public function getCompareNamespacesData(): array
    {
        return [
            ['GroupOne', 'OtherGroup', -8],
            ['One', 'Two', -5],
        ];
    }
}
