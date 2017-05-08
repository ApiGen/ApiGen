<?php declare(strict_types=1);

namespace ApiGen\Tests\Parser\Parser;

use ApiGen\Contracts\Parser\ParserInterface;
use ApiGen\Contracts\Parser\ParserStorageInterface;
use ApiGen\Reflection\Contract\Reflection\Class_\ClassReflectionInterface;
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
