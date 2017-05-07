<?php declare(strict_types=1);

namespace ApiGen\Tests\Parser\Reflection;

use ApiGen\Contracts\Parser\Reflection\FunctionReflectionInterface;
use ApiGen\Reflection\Contract\ParserInterface;
use ApiGen\Tests\AbstractContainerAwareTestCase;

// migrate to /Reflection package

final class ReflectionFunctionTest extends AbstractContainerAwareTestCase
{
    /**
     * @var FunctionReflectionInterface
     */
    private $reflectionFunction;

    protected function setUp(): void
    {
        /** @var ParserInterface $parser */
        $parser = $this->container->getByType(ParserInterface::class);
        $parser->parseDirectories([__DIR__ . '/ReflectionFunctionSource']);

        $reflectionStorage = $parser;

        $this->reflectionFunction = $reflectionStorage->getFunctions()['getSomeData'];
    }

    public function testIsDocumented(): void
    {
        $this->assertTrue($this->reflectionFunction->isDocumented());
    }
}
