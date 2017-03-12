<?php

namespace ApiGen\Parser\Tests\Reflection;

use ApiGen\Contracts\Parser\Reflection\Magic\MagicParameterReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ParameterReflectionInterface;
use ApiGen\Parser\Reflection\ReflectionFunctionBase;
use ApiGen\Parser\Reflection\ReflectionParameter;
use InvalidArgumentException;
use Mockery;
use TokenReflection\Broker;

class ReflectionParameterBaseTest extends AbstractReflectionTestCase
{

    /**
     * @var ReflectionFunctionBase
     */
    private $reflectionFunction;


    protected function setUp()
    {
        parent::setUp();
        $this->broker->processDirectory(__DIR__ . '/ReflectionFunctionSource');
        $this->reflectionFunction = $this->backend->getFunctions()['getSomeData'];
    }


    public function testGetShortName()
    {
        $this->assertSame('getSomeData', $this->reflectionFunction->getShortName());
    }


    public function testReturnReference()
    {
        $this->assertFalse($this->reflectionFunction->returnsReference());
    }


    public function testGetParameters()
    {
        $parameters = $this->reflectionFunction->getParameters();
        $this->assertCount(1, $parameters);

        /** @var ReflectionParameter $parameter */
        $parameter = $parameters[0];
        $this->assertInstanceOf(ParameterReflectionInterface::class, $parameter);
    }


    public function testGetParameter()
    {
        $parameter = $this->reflectionFunction->getParameter('arg');
        $this->assertInstanceOf(ParameterReflectionInterface::class, $parameter);

        $parameter = $this->reflectionFunction->getParameter(0);
        $this->assertInstanceOf(ParameterReflectionInterface::class, $parameter);
    }


    public function testGetParameterNotExistingName()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->reflectionFunction->getParameter('notHere');
    }


    public function testGetParameterNotExistingPosition()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->reflectionFunction->getParameter(1);
    }


    public function testProcessAnnotation()
    {
        $reflectionFunction = $this->backend->getFunctions()['withMagicParameters'];
        $parameters = $reflectionFunction->getParameters();

        $this->assertCount(2, $parameters);

        $this->assertInstanceOf(MagicParameterReflectionInterface::class, $parameters[0]);
        $this->assertInstanceOf(MagicParameterReflectionInterface::class, $parameters[1]);
    }


    public function testGetParametersAnnotationMatchingRealCount()
    {
        $reflectionFunction = $this->backend->getFunctions()['getMemoryInBytes'];

        $parameters = $reflectionFunction->getParameters();
        $this->assertCount(1, $parameters);

        $this->assertFalse($parameters[0]->isUnlimited());
    }
}
