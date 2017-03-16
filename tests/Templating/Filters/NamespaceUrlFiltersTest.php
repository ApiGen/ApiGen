<?php declare(strict_types=1);

namespace ApiGen\Tests\Templating\Filters;

use ApiGen\Configuration\Configuration;
use ApiGen\Parser\Elements\ElementStorage;
use ApiGen\Templating\Filters\Helpers\LinkBuilder;
use ApiGen\Templating\Filters\NamespaceUrlFilters;
use Mockery;
use PHPUnit\Framework\TestCase;

class NamespaceUrlFiltersTest extends TestCase
{

    /**
     * @var NamespaceUrlFilters
     */
    private $namespaceUrlFilters;


    protected function setUp(): void
    {
        $this->namespaceUrlFilters = new NamespaceUrlFilters(
            $this->getConfigurationMock(),
            new LinkBuilder,
            $this->getElementStorageMock(1, 1)
        );
    }


    public function testNamespaceUrl(): void
    {
        $this->assertSame(
            'namespace-Long.Namespace',
            $this->namespaceUrlFilters->namespaceUrl('Long\\Namespace')
        );
    }


    public function testNamespaceLinks(): void
    {
        $this->assertSame(
            '<a href="namespace-Long">Long</a>\<a href="namespace-Long.Namespace">Namespace</a>',
            $this->namespaceUrlFilters->namespaceLinks('Long\\Namespace')
        );

        $this->assertSame(
            '<a href="namespace-Long">Long</a>\Namespace',
            $this->namespaceUrlFilters->namespaceLinks('Long\\Namespace', false)
        );
    }


    public function testNamespaceLinksWithNoNamespaces(): void
    {
        $namespaceUrlFilters = new NamespaceUrlFilters(
            $this->getConfigurationMock(),
            new LinkBuilder,
            $this->getElementStorageMock(0, 0)
        );

        $this->assertSame('Long\\Namespace', $namespaceUrlFilters->namespaceLinks('Long\\Namespace'));
    }


    public function testSubgroupName(): void
    {
        $this->assertSame('Subgroup', $this->namespaceUrlFilters->subgroupName('Group\\Subgroup'));
        $this->assertSame('Group', $this->namespaceUrlFilters->subgroupName('Group'));
    }


    private function getConfigurationMock(): Mockery\MockInterface
    {
        $configurationMock = Mockery::mock(Configuration::class);
        $configurationMock->shouldReceive('getOption')->with('template')->andReturn([
            'templates' => [
                'package' => ['filename' => 'package-%s'],
                'namespace' => ['filename' => 'namespace-%s']
            ]
        ]);
        return $configurationMock;
    }


    private function getElementStorageMock($packageCount, $namespaceCount): Mockery\MockInterface
    {
        $elementStorageMock = Mockery::mock(ElementStorage::class);
        $elementStorageMock->shouldReceive('getPackages')->andReturn($packageCount);
        $elementStorageMock->shouldReceive('getNamespaces')->andReturn($namespaceCount);
        return $elementStorageMock;
    }
}
