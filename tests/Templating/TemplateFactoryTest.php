<?php declare(strict_types=1);

namespace ApiGen\Tests\Templating;

use ApiGen\Contracts\Configuration\ConfigurationInterface;
use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ConstantReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\FunctionReflectionInterface;
use ApiGen\Contracts\Templating\TemplateFactory\TemplateFactoryInterface;
use ApiGen\Templating\Template;
use ApiGen\Templating\TemplateFactory;
use ApiGen\Tests\AbstractContainerAwareTestCase;
use ApiGen\Tests\MethodInvoker;

final class TemplateFactoryTest extends AbstractContainerAwareTestCase
{
    /**
     * @var TemplateFactory
     */
    private $templateFactory;

    protected function setUp(): void
    {
        $this->templateFactory = $this->container->getByType(TemplateFactoryInterface::class);

        /** @var ConfigurationInterface $configuration */
        $configuration = $this->container->getByType(ConfigurationInterface::class);
        $configuration->resolveOptions([
           'source' => [__DIR__],
           'destination' => __DIR__ . '/Destination'
        ]);
    }

    public function testCreate(): void
    {
        $this->assertInstanceOf(Template::class, $this->templateFactory->create());
    }

    public function testCreateForType(): void
    {
        $this->assertInstanceOf(Template::class, $this->templateFactory->createForType('overview'));
    }

    public function testCreateNamedForElement(): void
    {
        $reflectionClassMock = $this->createMock(ClassReflectionInterface::class);

        $this->assertInstanceOf(
            Template::class,
            $this->templateFactory->createNamedForElement(TemplateFactory::ELEMENT_SOURCE, $reflectionClassMock)
        );
    }

    public function testCreateNamedForNamespace(): void
    {
        $this->assertInstanceOf(
            Template::class,
            $this->templateFactory->createNamedForElement(TemplateFactory::ELEMENT_NAMESPACE, 'Namespace')
        );
    }

    /**
     * @expectedException \Exception
     */
    public function testCreateNamedForElementNonExisting(): void
    {
        $reflectionClassMock = $this->createMock(ClassReflectionInterface::class);
        $this->templateFactory->createNamedForElement('notExisting', $reflectionClassMock);
    }

    public function testCreateForReflection(): void
    {
        $reflectionClassMock = $this->createMock(ClassReflectionInterface::class);
        $template = $this->templateFactory->createForReflection($reflectionClassMock);
        $this->assertInstanceOf(Template::class, $template);

        $reflectionConstantMock = $this->createMock(ConstantReflectionInterface::class);
        $template = $this->templateFactory->createForReflection($reflectionConstantMock);
        $this->assertInstanceOf(Template::class, $template);

        $reflectionFunctionMock = $this->createMock(FunctionReflectionInterface::class);
        $template = $this->templateFactory->createForReflection($reflectionFunctionMock);
        $this->assertInstanceOf(Template::class, $template);
    }

    public function testBuildTemplateCache(): void
    {
        $template = MethodInvoker::callMethodOnObject($this->templateFactory, 'create');
        $template2 = MethodInvoker::callMethodOnObject($this->templateFactory, 'create');
        $this->assertSame($template, $template2);
    }
}
