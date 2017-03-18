<?php declare(strict_types=1);

namespace ApiGen\Tests\Templating;

use ApiGen\Configuration\ConfigurationOptions as CO;
use ApiGen\Contracts\Configuration\ConfigurationInterface;
use ApiGen\Parser\Elements\AutocompleteElements;
use ApiGen\Parser\Elements\ElementStorage;
use ApiGen\Templating\Template;
use ApiGen\Templating\TemplateElementsLoader;
use Latte\Engine;
use Mockery;
use PHPUnit\Framework\TestCase;

final class TemplateElementsLoaderTest extends TestCase
{

    /**
     * @var TemplateElementsLoader
     */
    private $templateElementsLoader;


    protected function setUp(): void
    {
        $elementStorageMock = $this->getElementStorageMock();
        $configurationMock = $this->createMock(ConfigurationInterface::class);
        $configurationMock->method('getOption')->with(CO::ANNOTATION_GROUPS)->willReturn(['todo']);
        $configurationMock->method('getZipFileName')->willReturn('file.zip');

        $autocompleteElementsMock = $this->createMock(AutocompleteElements::class);
        $autocompleteElementsMock->method('getElements')->willReturn([]);

        $this->templateElementsLoader = new TemplateElementsLoader(
            $elementStorageMock,
            $configurationMock,
            $autocompleteElementsMock
        );
    }


    public function testAddElementToTemplate(): void
    {
        $latteEngineMock = $this->createMock(Engine::class);
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

        $this->assertSame(['todo'], $parameters['annotationGroups']);
    }


    private function getElementStorageMock(): Mockery\MockInterface
    {
        $elementStorageMock = $this->createMock(ElementStorage::class);
        $elementStorageMock->method('getNamespaces')->willReturn([]);
        $elementStorageMock->method('getPackages')->willReturn([]);
        $elementStorageMock->method('getClasses')->willReturn([]);
        $elementStorageMock->method('getInterfaces')->willReturn([]);
        $elementStorageMock->method('getTraits')->willReturn([]);
        $elementStorageMock->method('getExceptions')->willReturn([]);
        $elementStorageMock->method('getConstants')->willReturn([]);
        $elementStorageMock->method('getFunctions')->willReturn([]);
        return $elementStorageMock;
    }
}
