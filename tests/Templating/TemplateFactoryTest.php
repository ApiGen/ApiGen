<?php declare(strict_types=1);

namespace ApiGen\Tests\Templating;

use ApiGen;
use ApiGen\Contracts\Configuration\ConfigurationInterface;
use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ConstantReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\FunctionReflectionInterface;
use ApiGen\Contracts\Templating\TemplateFactory\TemplateFactoryInterface;
use ApiGen\Templating\Template;
use ApiGen\Templating\TemplateFactory;
use ApiGen\Templating\TemplateNavigator;
use ApiGen\Tests\MethodInvoker;
use Latte\Engine;
use PHPUnit\Framework\TestCase;

final class TemplateFactoryTest extends TestCase
{

    /**
     * @var TemplateFactory
     */
    private $templateFactory;


    protected function setUp(): void
    {
        $container = (new ApiGen\Tests\ContainerFactory)->create();
        $this->templateFactory = $container->getByType(TemplateFactoryInterface::class);

        /** @var ConfigurationInterface $configuration */
        $configuration = $container->getByType(ConfigurationInterface::class);
        $configuration->resolveOptions([
           'source' => __DIR__,
           'destination' => __DIR__ . '/Destination'
        ]);


//        $latteEngineMock = $this->createMock(Engine::class);
//
//        $configurationMock = $this->createMock(ConfigurationInterface::class);
//        $configurationMock->method('getOptions')->willReturn(['template' => ['templatesPath' => '...']]);
//
//        $templateElementsLoaderMock = $this->createMock(ApiGen\Templating\TemplateElementsLoader::class);
//        $templateElementsLoaderMock->method('addElementsToTemplate')->willReturnCallback(function ($args) {
//            return $args;
//        });
//
//        $this->templateFactory = new TemplateFactory(
//            $latteEngineMock,
//            $configurationMock,
//            $this->getTemplateNavigatorMock(),
//            $templateElementsLoaderMock
//        );
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

        $this->assertInstanceOf(
            Template::class,
            $this->templateFactory->createNamedForElement(TemplateFactory::ELEMENT_NAMESPACE, $reflectionClassMock)
        );
    }

//
//    public function testCreateNamedForElementNonExisting(): void
//    {
//        $reflectionClassMock = $this->createMock(ClassReflectionInterface::class);
//        $this->assertInstanceOf(
//            'ApiGen\Templating\Template',
//            $this->templateFactory->createNamedForElement('notExisting', $reflectionClassMock)
//        );
//    }
//
//
//    public function testCreateForReflection(): void
//    {
//        $reflectionClassMock = $this->createMock(ClassReflectionInterface::class);
//        $template = $this->templateFactory->createForReflection($reflectionClassMock);
//        $this->assertInstanceOf(Template::class, $template);
//
//        $reflectionConstantMock = $this->createMock(ConstantReflectionInterface::class);
//        $template = $this->templateFactory->createForReflection($reflectionConstantMock);
//        $this->assertInstanceOf(Template::class, $template);
//
//        $reflectionFunctionMock = $this->createMock(FunctionReflectionInterface::class);
//        $template = $this->templateFactory->createForReflection($reflectionFunctionMock);
//        $this->assertInstanceOf(Template::class, $template);
//    }
//
//
//    public function testBuildTemplateCache(): void
//    {
//        $template = MethodInvoker::callMethodOnObject($this->templateFactory, 'buildTemplate');
//        $template2 = MethodInvoker::callMethodOnObject($this->templateFactory, 'buildTemplate');
//        $this->assertSame($template, $template2);
//    }
//
//
//    private function getTemplateNavigatorMock(): Mockery\MockInterface
//    {
//        $templateNavigatorMock = $this->createMock(TemplateNavigator::class);
//        $templateNavigatorMock->method('getTemplatePath')->willReturnCallback(function ($arg) {
//            return $arg . '-template-path.latte';
//        });
//        $templateNavigatorMock->method('getTemplateFileName')->willReturn('...');
//        $templateNavigatorMock->method('getTemplatePathForClass')->willReturn();
//        $templateNavigatorMock->method('getTemplatePathForConstant')->willReturn();
//        $templateNavigatorMock->method('getTemplatePathForFunction')->willReturn();
//        $templateNavigatorMock->method('getTemplatePathForSourceElement')->willReturn();
//        $templateNavigatorMock->method('getTemplatePathForNamespace')->willReturn();
//        $templateNavigatorMock->method('getTemplatePathForPackage')->willReturn();
//
//        return $templateNavigatorMock;
//    }
}
