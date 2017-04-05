<?php declare(strict_types=1);

namespace ApiGen\Tests\Templating;

use ApiGen\Contracts\Configuration\ConfigurationInterface;
use ApiGen\Templating\Template;
use ApiGen\Templating\TemplateElementsLoader;
use ApiGen\Tests\AbstractContainerAwareTestCase;
use Latte\Engine;

final class TemplateElementsLoaderTest extends AbstractContainerAwareTestCase
{
    /**
     * @var TemplateElementsLoader
     */
    private $templateElementsLoader;

    protected function setUp(): void
    {
        $this->templateElementsLoader = $this->container->getByType(TemplateElementsLoader::class);

        $configuration = $this->container->getByType(ConfigurationInterface::class);
        $configuration->resolveOptions([
            'source' => [__DIR__],
            'destination' => TEMP_DIR,
            'annotationGroups' => ['todo']
        ]);
    }

    public function testAddElementToTemplate(): void
    {
        $latteEngineMock = $this->createMock(Engine::class);
        $template = new Template($latteEngineMock);
        $this->templateElementsLoader->addElementsToTemplate($template);
        $this->assertInstanceOf(Template::class, $template);

        $parameters = $template->getParameters();
        $this->assertArrayHasKey('namespace', $parameters);
        $this->assertArrayHasKey('class', $parameters);
        $this->assertArrayHasKey('constant', $parameters);
        $this->assertArrayHasKey('function', $parameters);
        $this->assertArrayHasKey('namespaces', $parameters);
        $this->assertArrayHasKey('classes', $parameters);
        $this->assertArrayHasKey('interfaces', $parameters);
        $this->assertArrayHasKey('traits', $parameters);
        $this->assertArrayHasKey('exceptions', $parameters);
        $this->assertArrayHasKey('functions', $parameters);
        $this->assertArrayHasKey('elements', $parameters);

        $this->assertSame(['todo'], $parameters['annotationGroups']);
    }
}
