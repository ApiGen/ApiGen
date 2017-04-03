<?php declare(strict_types=1);

namespace ApiGen\Parser\Tests\Reflection;

use ApiGen\Contracts\Parser\ParserInterface;
use ApiGen\Contracts\Parser\ParserStorageInterface;
use ApiGen\Parser\Reflection\AbstractReflection;
use ApiGen\Tests\AbstractContainerAwareTestCase;
use ApiGen\Tests\MethodInvoker;
use Project\ReflectionMethod;

final class AbstractReflectionTest extends AbstractContainerAwareTestCase
{
    /**
     * @var AbstractReflection
     */
    private $reflectionClass;

    protected function setUp(): void
    {
        /** @var ParserInterface $parser */
        $parser = $this->container->getByType(ParserInterface::class);
        $parserStorage = $parser->parseDirectories([__DIR__ . '/ReflectionMethodSource']);

        $this->reflectionClass = $parserStorage->getClasses()[ReflectionMethod::class];

        /** @var ParserStorageInterface $parserStorage */
        $parserStorage = $this->container->getByType(ParserStorageInterface::class);
        $parserStorage->setClasses([
            ReflectionMethod::class => $this->reflectionClass
        ]);
    }

    public function testGetName(): void
    {
        $this->assertSame(ReflectionMethod::class, $this->reflectionClass->getName());
    }

    public function testGetPrettyName(): void
    {
        $this->assertSame(ReflectionMethod::class, $this->reflectionClass->getPrettyName());
    }

    public function testIsInternal(): void
    {
        $this->assertFalse($this->reflectionClass->isInternal());
    }

    public function testGetFileName(): void
    {
        $this->assertStringEndsWith('ReflectionMethod.php', $this->reflectionClass->getFileName());
    }

    public function testGetStartLine(): void
    {
        $this->assertSame(23, $this->reflectionClass->getStartLine());
    }

    public function testGetEndLine(): void
    {
        $this->assertSame(40, $this->reflectionClass->getEndLine());
    }

    public function testGetParsedClasses(): void
    {
        $parsedClasses = MethodInvoker::callMethodOnObject($this->reflectionClass, 'getParsedClasses');
        $this->assertCount(1, $parsedClasses);
    }
}
