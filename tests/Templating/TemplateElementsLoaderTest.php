<?php

namespace ApiGen\Tests\Templating;

use ApiGen\Configuration\Configuration;
use ApiGen\Configuration\ConfigurationOptions as CO;
use ApiGen\Parser\Elements\AutocompleteElements;
use ApiGen\Parser\Elements\ElementStorage;
use ApiGen\Templating\Template;
use ApiGen\Templating\TemplateElementsLoader;
use Latte\Engine;
use Mockery;
use PHPUnit\Framework\TestCase;

class TemplateElementsLoaderTest extends TestCase
{

    /**
     * @var TemplateElementsLoader
     */
    private $templateElementsLoader;


    protected function setUp()
    {
        $elementStorageMock = $this->getElementStorageMock();
        $configurationMock = Mockery::mock(Configuration::class);
        $configurationMock->shouldReceive('getOption')->with(CO::DOWNLOAD)->andReturn(true);
        $configurationMock->shouldReceive('getOption')->with(CO::ANNOTATION_GROUPS)->andReturn(['todo']);
        $configurationMock->shouldReceive('getZipFileName')->andReturn('file.zip');

        $autocompleteElementsMock = Mockery::mock(AutocompleteElements::class);
        $autocompleteElementsMock->shouldReceive('getElements')->andReturn([]);

        $this->templateElementsLoader = new TemplateElementsLoader(
            $elementStorageMock,
            $configurationMock,
            $autocompleteElementsMock
        );
    }


    public function testAddElementToTemplate()
    {
        $latteEngineMock = Mockery::mock(Engine::class);
        $template = new Template($latteEngineMock);
        $template = $this->templateElementsLoader->addElementsToTemplate($template);
        $this->assertInstanceOf(Template::class, $template);

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
        $this->assertSame(['todo'], $parameters['annotationGroups']);
    }


    /**
     * @return Mockery\MockInterface
     */
    private function getElementStorageMock()
    {
        $elementStorageMock = Mockery::mock(ElementStorage::class);
        $elementStorageMock->shouldReceive('getNamespaces')->andReturn([]);
        $elementStorageMock->shouldReceive('getPackages')->andReturn([]);
        $elementStorageMock->shouldReceive('getClasses')->andReturn([]);
        $elementStorageMock->shouldReceive('getInterfaces')->andReturn([]);
        $elementStorageMock->shouldReceive('getTraits')->andReturn([]);
        $elementStorageMock->shouldReceive('getExceptions')->andReturn([]);
        $elementStorageMock->shouldReceive('getConstants')->andReturn([]);
        $elementStorageMock->shouldReceive('getFunctions')->andReturn([]);
        return $elementStorageMock;
    }
}
