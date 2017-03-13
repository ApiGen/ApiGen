<?php

namespace ApiGen\Tests\Templating\Filters;

use ApiGen\Configuration\Configuration;
use ApiGen\Parser\Elements\ElementStorage;
use ApiGen\Templating\Filters\Helpers\LinkBuilder;
use ApiGen\Templating\Filters\NamespaceAndPackageUrlFilters;
use Mockery;
use PHPUnit\Framework\TestCase;

class NamespaceAndPackageUrlFiltersTest extends TestCase
{

    /**
     * @var NamespaceAndPackageUrlFilters
     */
    private $namespaceAndPackageUrlFilters;


    protected function setUp()
    {
        $this->namespaceAndPackageUrlFilters = new NamespaceAndPackageUrlFilters(
            $this->getConfigurationMock(),
            new LinkBuilder,
            $this->getElementStorageMock(1, 1)
        );
    }


    public function testNamespaceUrl()
    {
        $this->assertSame(
            'namespace-Long.Namespace',
            $this->namespaceAndPackageUrlFilters->namespaceUrl('Long\\Namespace')
        );
    }


    public function testNamespaceLinks()
    {
        $this->assertSame(
            '<a href="namespace-Long">Long</a>\<a href="namespace-Long.Namespace">Namespace</a>',
            $this->namespaceAndPackageUrlFilters->namespaceLinks('Long\\Namespace')
        );

        $this->assertSame(
            '<a href="namespace-Long">Long</a>\Namespace',
            $this->namespaceAndPackageUrlFilters->namespaceLinks('Long\\Namespace', false)
        );
    }


    public function testNamespaceLinksWithNoNamespaces()
    {
        $namespaceAndPackageUrlFilters = new NamespaceAndPackageUrlFilters(
            $this->getConfigurationMock(),
            new LinkBuilder,
            $this->getElementStorageMock(0, 0)
        );

        $this->assertSame('Long\\Namespace', $namespaceAndPackageUrlFilters->namespaceLinks('Long\\Namespace'));
    }


    public function testSubgroupName()
    {
        $this->assertSame('Subgroup', $this->namespaceAndPackageUrlFilters->subgroupName('Group\\Subgroup'));
        $this->assertSame('Group', $this->namespaceAndPackageUrlFilters->subgroupName('Group'));
    }


    /**
     * @return Mockery\MockInterface
     */
    private function getConfigurationMock()
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


    /**
     * @return Mockery\MockInterface
     */
    private function getElementStorageMock($packageCount, $namespaceCount)
    {
        $elementStorageMock = Mockery::mock(ElementStorage::class);
        $elementStorageMock->shouldReceive('getPackages')->andReturn($packageCount);
        $elementStorageMock->shouldReceive('getNamespaces')->andReturn($namespaceCount);
        return $elementStorageMock;
    }
}
