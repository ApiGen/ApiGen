<?php declare(strict_types=1);

namespace ApiGen\Parser\Tests\Reflection;

use ApiGen\Contracts\Parser\Reflection\ParameterReflectionInterface;
use ApiGen\Parser\Reflection\AbstractReflectionFunction;
use ApiGen\Parser\Reflection\ReflectionParameter;

final class AbstractReflectionFunctionTest extends AbstractReflectionTestCase
{
    /**
     * @var AbstractReflectionFunction
     */
    private $reflectionFunction;

    protected function setUp(): void
    {
        parent::setUp();
        $this->broker->processDirectory(__DIR__ . '/ReflectionFunctionSource');
        $this->reflectionFunction = $this->backend->getFunctions()['getSomeData'];
    }

    public function testGetShortName(): void
    {
        $this->assertSame('getSomeData', $this->reflectionFunction->getShortName());
    }

    public function testReturnReference(): void
    {
        $this->assertFalse($this->reflectionFunction->returnsReference());
    }

    public function testGetParameters(): void
    {
        $parameters = $this->reflectionFunction->getParameters();
        $this->assertCount(1, $parameters);

        /** @var ReflectionParameter $parameter */
        $parameter = $parameters[0];
        $this->assertInstanceOf(ParameterReflectionInterface::class, $parameter);
    }

    public function testGetParametersAnnotationMatchingRealCount(): void
    {
        $reflectionFunction = $this->backend->getFunctions()['getMemoryInBytes'];

        $parameters = $reflectionFunction->getParameters();
        $this->assertCount(1, $parameters);

        $this->assertFalse($parameters[0]->isVariadic());
    }
}
