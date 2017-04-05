<?php declare(strict_types=1);

namespace ApiGen\ReflectionToElementTransformer\Tests;

use ApiGen\Contracts\Configuration\ConfigurationInterface;
use ApiGen\Contracts\Parser\ParserStorageInterface;
use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\FunctionReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\MethodReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ParameterReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\PropertyReflectionInterface;
use ApiGen\ReflectionToElementTransformer\Contract\TransformerCollectorInterface;
use ApiGen\Tests\AbstractContainerAwareTestCase;
use Nette\Object;
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
        $tokenReflectionClassMock = $this->createMock(IReflectionClass::class, Object::class);
        $reflectionClass = $this->transformerCollector->transformReflectionToElement($tokenReflectionClassMock);
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

        $reflectionFunction = $this->transformerCollector->transformReflectionToElement($tokenReflectionFunctionMock);
        $this->assertInstanceOf(FunctionReflectionInterface::class, $reflectionFunction);
    }

    public function testCreateFromReflectionMethod(): void
    {
        $tokenReflectionMethodMock = $this->createMock(IReflectionMethod::class, Object::class);
        $reflectionMethod = $this->transformerCollector->transformReflectionToElement($tokenReflectionMethodMock);
        $this->assertInstanceOf(MethodReflectionInterface::class, $reflectionMethod);
        $this->checkLoadedProperties($reflectionMethod);
    }

    public function testCreateFromReflectionProperty(): void
    {
        $tokenReflectionPropertyMock = $this->createMock(IReflectionProperty::class, Object::class);
        $reflectionProperty = $this->transformerCollector->transformReflectionToElement($tokenReflectionPropertyMock);
        $this->assertInstanceOf(PropertyReflectionInterface::class, $reflectionProperty);
        $this->checkLoadedProperties($reflectionProperty);
    }

    public function testCreateFromReflectionParameter(): void
    {
        $tokenReflectionParameterMock = $this->createMock(ReflectionParameter::class);

        $reflectionParameter = $this->transformerCollector->transformReflectionToElement($tokenReflectionParameterMock);
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
            ParserStorageInterface::class,
            Assert::getObjectAttribute($object, 'parserStorage')
        );

        $this->assertInstanceOf(
            TransformerCollectorInterface::class,
            Assert::getObjectAttribute($object, 'transformerCollector')
        );
    }
}
