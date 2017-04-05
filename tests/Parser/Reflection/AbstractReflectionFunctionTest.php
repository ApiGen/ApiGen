<?php declare(strict_types=1);

namespace ApiGen\Parser\Tests\Reflection;

use ApiGen\Contracts\Parser\ParserInterface;
use ApiGen\Contracts\Parser\Reflection\ParameterReflectionInterface;
use ApiGen\Parser\Reflection\AbstractReflectionFunction;
use ApiGen\Tests\AbstractContainerAwareTestCase;

final class AbstractReflectionFunctionTest extends AbstractContainerAwareTestCase
{
    /**
     * @var AbstractReflectionFunction
     */
    private $reflectionFunction;

    /**
     * @var AbstractReflectionFunction
     */
    private $otherReflectionFunction;

    protected function setUp(): void
    {
        /** @var ParserInterface $parser */
        $parser = $this->container->getByType(ParserInterface::class);

        $parserStorage = $parser->parseDirectories([__DIR__ . '/ReflectionFunctionSource']);
        $this->reflectionFunction = $parserStorage->getFunctions()['getSomeData'];
        $this->otherReflectionFunction = $parserStorage->getFunctions()['getMemoryInBytes'];
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

        $parameter = $parameters[0];
        $this->assertInstanceOf(ParameterReflectionInterface::class, $parameter);
    }

    public function testGetParametersAnnotationMatchingRealCount(): void
    {
        $parameters = $this->otherReflectionFunction->getParameters();
        $this->assertCount(1, $parameters);

        $this->assertFalse($parameters[0]->isVariadic());
    }
}
