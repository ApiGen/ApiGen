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
use Mockery;
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


//        $latteEngineMock = Mockery::mock(Engine::class);
//
//        $configurationMock = Mockery::mock(ConfigurationInterface::class);
//        $configurationMock->shouldReceive('getOptions')->andReturn(['template' => ['templatesPath' => '...']]);
//
//        $templateElementsLoaderMock = Mockery::mock(ApiGen\Templating\TemplateElementsLoader::class);
//        $templateElementsLoaderMock->shouldReceive('addElementsToTemplate')->andReturnUsing(function ($args) {
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
//        $reflectionClassMock = Mockery::mock(ClassReflectionInterface::class);
//        $this->assertInstanceOf(
//            'ApiGen\Templating\Template',
//            $this->templateFactory->createNamedForElement('notExisting', $reflectionClassMock)
//        );
//    }
//
//
//    public function testCreateForReflection(): void
//    {
//        $reflectionClassMock = Mockery::mock(ClassReflectionInterface::class);
//        $template = $this->templateFactory->createForReflection($reflectionClassMock);
//        $this->assertInstanceOf(Template::class, $template);
//
//        $reflectionConstantMock = Mockery::mock(ConstantReflectionInterface::class);
//        $template = $this->templateFactory->createForReflection($reflectionConstantMock);
//        $this->assertInstanceOf(Template::class, $template);
//
//        $reflectionFunctionMock = Mockery::mock(FunctionReflectionInterface::class);
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
//        $templateNavigatorMock = Mockery::mock(TemplateNavigator::class);
//        $templateNavigatorMock->shouldReceive('getTemplatePath')->andReturnUsing(function ($arg) {
//            return $arg . '-template-path.latte';
//        });
//        $templateNavigatorMock->shouldReceive('getTemplateFileName')->andReturn('...');
//        $templateNavigatorMock->shouldReceive('getTemplatePathForClass')->andReturn();
//        $templateNavigatorMock->shouldReceive('getTemplatePathForConstant')->andReturn();
//        $templateNavigatorMock->shouldReceive('getTemplatePathForFunction')->andReturn();
//        $templateNavigatorMock->shouldReceive('getTemplatePathForSourceElement')->andReturn();
//        $templateNavigatorMock->shouldReceive('getTemplatePathForNamespace')->andReturn();
//        $templateNavigatorMock->shouldReceive('getTemplatePathForPackage')->andReturn();
//
//        return $templateNavigatorMock;
//    }
}
