<?php declare(strict_types=1);

namespace ApiGen\Parser\Tests\Reflection;

use ApiGen\Contracts\Parser\ParserInterface;
use ApiGen\Contracts\Parser\Reflection\FunctionReflectionInterface;
use ApiGen\Tests\AbstractContainerAwareTestCase;

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
        $parserStorage = $parser->parseDirectories([__DIR__ . '/ReflectionFunctionSource']);

        $this->reflectionFunction = $parserStorage->getFunctions()['getSomeData'];
    }

    public function testIsDocumented(): void
    {
        $this->assertTrue($this->reflectionFunction->isDocumented());
    }
}
