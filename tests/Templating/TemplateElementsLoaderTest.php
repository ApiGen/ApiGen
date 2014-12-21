<?php

namespace ApiGen\Tests\Templating;

use ApiGen\Configuration\ConfigurationOptions as CO;
use ApiGen\Templating\Template;
use ApiGen\Templating\TemplateElementsLoader;
use Mockery;
use PHPUnit_Framework_TestCase;


class TemplateElementsLoaderTest extends PHPUnit_Framework_TestCase
{

	/**
	 * @var TemplateElementsLoader
	 */
	private $templateElementsLoader;


	protected function setUp()
	{
		$elementStorageMock = $this->getElementStorageMock();
		$configurationMock = Mockery::mock('ApiGen\Configuration\Configuration');
		$configurationMock->shouldReceive('getOption')->with(CO::DOWNLOAD)->andReturn(TRUE);
		$configurationMock->shouldReceive('getZipFileName')->andReturn('file.zip');

		$autocompleteElementsMock = Mockery::mock('ApiGen\Parser\Elements\AutocompleteElements');
		$autocompleteElementsMock->shouldReceive('getElements')->andReturn([]);

		$this->templateElementsLoader = new TemplateElementsLoader(
			$elementStorageMock, $configurationMock, $autocompleteElementsMock
		);
	}


	public function testAddElementToTemplate()
	{
		$latteEngineMock = Mockery::mock('Latte\Engine');
		$template = new Template($latteEngineMock);
		$template = $this->templateElementsLoader->addElementsToTemplate($template);
		$this->assertInstanceOf('ApiGen\Templating\Template', $template);

		$parameters = $template->getParameters();
		$this->assertArrayHasKey('namespace', $parameters);
		$this->assertArrayHasKey('namespace', $parameters);
		$this->assertArrayHasKey('package', $parameters);
		$this->assertArrayHasKey('class', $parameters);
		$this->assertArrayHasKey('constant', $parameters);
		$this->assertArrayHasKey('function', $parameters);
		$this->assertArrayHasKey('namespaces', $parameters);
		$this->assertArrayHasKey('packages', $parameters);
		$this->assertArrayHasKey('classes', $parameters);
		$this->assertArrayHasKey('interfaces', $parameters);
		$this->assertArrayHasKey('traits', $parameters);
		$this->assertArrayHasKey('exceptions', $parameters);
		$this->assertArrayHasKey('constants', $parameters);
		$this->assertArrayHasKey('functions', $parameters);
		$this->assertArrayHasKey('elements', $parameters);

		$this->assertSame('file.zip', $parameters['archive']);
	}


	/**
	 * @return Mockery\MockInterface
	 */
	private function getElementStorageMock()
	{
		$elementStorageMock = Mockery::mock('ApiGen\Parser\Elements\ElementStorage');
		$elementStorageMock->shouldReceive('getNamespaces')->andReturn([]);
		$elementStorageMock->shouldReceive('getPackages')->andReturn([]);
		$elementStorageMock->shouldReceive('getClasses')->andReturn([]);
		$elementStorageMock->shouldReceive('getClasses')->andReturn([]);
		$elementStorageMock->shouldReceive('getTraits')->andReturn([]);
		$elementStorageMock->shouldReceive('getExceptions')->andReturn([]);
		$elementStorageMock->shouldReceive('getConstants')->andReturn([]);
		$elementStorageMock->shouldReceive('getFunctions')->andReturn([]);
		return $elementStorageMock;
	}

}
