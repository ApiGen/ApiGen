<?php

namespace ApiGen\Tests\Templating\Filters;

use ApiGen\Templating\Filters\Helpers\LinkBuilder;
use ApiGen\Templating\Filters\NamespaceAndPackageUrlFilters;
use Mockery;
use PHPUnit_Framework_TestCase;


class NamespaceAndPackageUrlFiltersTest extends PHPUnit_Framework_TestCase
{

	/**
	 * @var NamespaceAndPackageUrlFilters
	 */
	private $namespaceAndPackageUrlFilters;


	protected function setUp()
	{
		$this->namespaceAndPackageUrlFilters = new NamespaceAndPackageUrlFilters(
			$this->getConfigurationMock(), new LinkBuilder, $this->getElementStorageMock(1, 1)
		);
	}


	public function testPackageUrl()
	{
		$this->assertSame(
			'package-Long.Package',
			$this->namespaceAndPackageUrlFilters->packageUrl('Long\\Package')
		);
	}


	public function testPackageLinks()
	{
		$this->assertSame(
			'<a href="package-Long">Long</a>\<a href="package-Long.Package">Package</a>',
			$this->namespaceAndPackageUrlFilters->packageLinks('Long\\Package')
		);

		$this->assertSame(
			'<a href="package-Long">Long</a>\Package',
			$this->namespaceAndPackageUrlFilters->packageLinks('Long\\Package', FALSE)
		);
	}


	public function testPackageLinksWithNoPackages()
	{
		$namespaceAndPackageUrlFilters = new NamespaceAndPackageUrlFilters(
			$this->getConfigurationMock(), new LinkBuilder, $this->getElementStorageMock(0, 0)
		);

		$this->assertSame('Long\\Package', $namespaceAndPackageUrlFilters->packageLinks('Long\\Package'));
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
			$this->namespaceAndPackageUrlFilters->namespaceLinks('Long\\Namespace', FALSE)
		);
	}


	public function testNamespaceLinksWithNoNamespaces()
	{
		$namespaceAndPackageUrlFilters = new NamespaceAndPackageUrlFilters(
			$this->getConfigurationMock(), new LinkBuilder, $this->getElementStorageMock(0, 0)
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
		$configurationMock = Mockery::mock('ApiGen\Configuration\Configuration');
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
		$elementStorageMock = Mockery::mock('ApiGen\Parser\Elements\ElementStorage');
		$elementStorageMock->shouldReceive('getPackages')->andReturn($packageCount);
		$elementStorageMock->shouldReceive('getNamespaces')->andReturn($namespaceCount);
		return $elementStorageMock;
	}

}
