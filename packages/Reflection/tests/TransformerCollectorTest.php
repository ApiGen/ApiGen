<?php declare(strict_types=1);

namespace ApiGen\Reflection\Tests;

use ApiGen\Contracts\Configuration\ConfigurationInterface;
use ApiGen\Reflection\Contract\Reflection\Class_\ClassReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Function_\FunctionReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\ClassMethodReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\ParameterReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\ClassPropertyReflectionInterface;
use ApiGen\Reflection\Contract\TransformerCollectorInterface;
use ApiGen\Tests\AbstractContainerAwareTestCase;
use PHPUnit\Framework\Assert;
use Roave\BetterReflection\Reflection\ReflectionFunction;
use Roave\BetterReflection\Reflection\ReflectionParameter;
use TokenReflection\IReflectionClass;
use TokenReflection\IReflectionMethod;
use TokenReflection\IReflectionProperty;

final class TransformerCollectorTest extends AbstractContainerAwareTestCase
{
    /**
     * @var TransformerCollectorInterface
     */
    private $transformerCollector;

    protected function setUp(): void
    {
        $this->transformerCollector = $this->container->getByType(TransformerCollectorInterface::class);
    }

    public function testCreateFromReflectionClass(): void
    {
        $tokenReflectionClassMock = $this->createMock(IReflectionClass::class);
        $reflectionClass = $this->transformerCollector->transformSingle($tokenReflectionClassMock);
        $this->assertInstanceOf(ClassReflectionInterface::class, $reflectionClass);
        $this->checkLoadedProperties($reflectionClass);
    }

    public function testCreateFromReflectionFunction(): void
    {
        $tokenReflectionFunctionMock = $this->createMock(ReflectionFunction::class);
        $tokenReflectionFunctionMock->method('getParameters')
            ->willReturn([]);
        $tokenReflectionFunctionMock->method('getDocComment')
            ->willReturn(' ');

        $reflectionFunction = $this->transformerCollector->transformSingle($tokenReflectionFunctionMock);
        $this->assertInstanceOf(FunctionReflectionInterface::class, $reflectionFunction);
    }

    public function testCreateFromReflectionMethod(): void
    {
        $tokenReflectionMethodMock = $this->createMock(IReflectionMethod::class);
        $reflectionMethod = $this->transformerCollector->transformSingle($tokenReflectionMethodMock);
        $this->assertInstanceOf(ClassMethodReflectionInterface::class, $reflectionMethod);
        $this->checkLoadedProperties($reflectionMethod);
    }

    public function testCreateFromReflectionProperty(): void
    {
        $tokenReflectionPropertyMock = $this->createMock(IReflectionProperty::class);
        $reflectionProperty = $this->transformerCollector->transformSingle($tokenReflectionPropertyMock);
        $this->assertInstanceOf(ClassPropertyReflectionInterface::class, $reflectionProperty);
        $this->checkLoadedProperties($reflectionProperty);
    }

    public function testCreateFromReflectionParameter(): void
    {
        $tokenReflectionParameterMock = $this->createMock(ReflectionParameter::class);

        $reflectionParameter = $this->transformerCollector->transformSingle($tokenReflectionParameterMock);
        $this->assertInstanceOf(ParameterReflectionInterface::class, $reflectionParameter);
    }

    /**
     * @param object $object
     */
    private function checkLoadedProperties($object): void
    {
        $this->assertInstanceOf(
            ConfigurationInterface::class,
            Assert::getObjectAttribute($object, 'configuration')
        );

        $this->assertInstanceOf(
            TransformerCollectorInterface::class,
            Assert::getObjectAttribute($object, 'transformerCollector')
        );
    }
}
