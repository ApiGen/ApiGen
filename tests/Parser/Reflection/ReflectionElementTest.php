<?php declare(strict_types=1);

namespace ApiGen\Parser\Tests\Reflection;

use ApiGen\Contracts\Parser\ParserInterface;
use ApiGen\Contracts\Parser\Reflection\ElementReflectionInterface;
use ApiGen\Tests\AbstractContainerAwareTestCase;
use Project\ReflectionMethod;

final class ReflectionElementTest extends AbstractContainerAwareTestCase
{
    /**
     * @var ElementReflectionInterface
     */
    private $reflectionClass;

    protected function setUp(): void
    {
        /** @var ParserInterface $parser */
        $parser = $this->container->getByType(ParserInterface::class);
        $parserStorage = $parser->parseDirectories([__DIR__ . '/ReflectionMethodSource']);

        $this->reflectionClass = $parserStorage->getClasses()[ReflectionMethod::class];
    }

    public function testIsDocumented(): void
    {
        $this->assertTrue($this->reflectionClass->isDocumented());
    }

    public function testIsDeprecated(): void
    {
        $this->assertFalse($this->reflectionClass->isDeprecated());
    }

    public function testGetNamespaceName(): void
    {
        $this->assertSame('Project', $this->reflectionClass->getNamespaceName());
    }

    public function testGetPseudoNamespaceName(): void
    {
        $this->assertSame('Project', $this->reflectionClass->getPseudoNamespaceName());
    }

    public function testGetDescription(): void
    {
        $this->assertSame('This is some description', $this->reflectionClass->getDescription());
    }

    public function testGetAnnotations(): void
    {
        $annotations = $this->reflectionClass->getAnnotations();
        $this->assertCount(3, $annotations);
        $this->assertArrayHasKey('property-read', $annotations);
        $this->assertArrayHasKey('method', $annotations);
        $this->assertArrayHasKey('package', $annotations);
    }

    public function testGetAnnotation(): void
    {
        $this->assertSame(['Some_Package'], $this->reflectionClass->getAnnotation('package'));
    }

    public function testHasAnnotation(): void
    {
        $this->assertTrue($this->reflectionClass->hasAnnotation('package'));
        $this->assertFalse($this->reflectionClass->hasAnnotation('nope'));
    }
}
