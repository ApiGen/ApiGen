<?php declare(strict_types=1);

namespace ApiGen\Parser\Tests;

use ApiGen\Contracts\Parser\ParserInterface;
use ApiGen\Contracts\Parser\ParserStorageInterface;
use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ConstantReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\FunctionReflectionInterface;
use ApiGen\Tests\AbstractContainerAwareTestCase;

final class ParserTest extends AbstractContainerAwareTestCase
{
    /**
     * @var ParserInterface
     */
    private $parser;

    /**
     * @var ParserStorageInterface
     */
    private $parserStorage;

    protected function setUp(): void
    {
        $this->parser = $this->container->getByType(ParserInterface::class);
        $this->parserStorage = $this->container->getByType(ParserStorageInterface::class);
    }

    public function testGetFunctions(): void
    {
        $this->parser->parseDirectories([__DIR__ . '/ParserSource']);
        $functions = $this->parserStorage->getFunctions();
        $this->assertCount(1, $functions);

        $function = array_pop($functions);
        $this->assertInstanceOf(FunctionReflectionInterface::class, $function);
    }

    /**
     * @expectedException \TokenReflection\Exception\FileProcessingException
     */
    public function testParseError(): void
    {
        $this->assertCount(0, $this->parserStorage->getClasses());

        $this->parser->parseDirectories([__DIR__ . '/ErrorParseSource']);
    }

    public function testParseClasses(): void
    {
        $this->assertCount(0, $this->parserStorage->getClasses());

        $this->parser->parseDirectories([__DIR__ . '/ParserSource']);

        $classes = $this->parserStorage->getClasses();
        $this->assertCount(4, $classes);

        $class = array_pop($classes);
        $this->assertInstanceOf(ClassReflectionInterface::class, $class);
    }
}
