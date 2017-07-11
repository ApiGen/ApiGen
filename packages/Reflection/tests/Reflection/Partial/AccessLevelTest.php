<?php declare(strict_types=1);

namespace ApiGen\Reflection\Tests\Reflection\Partial;

use ApiGen\Reflection\Contract\Reflection\Partial\AccessLevelInterface;
use ApiGen\Reflection\Tests\Reflection\Partial\Source\SomeClassWithAnnotations;
use ApiGen\Tests\AbstractParserAwareTestCase;

final class AccessLevelTest extends AbstractParserAwareTestCase
{
    /**
     * @var AccessLevelInterface
     */
    private $reflection;

    protected function setUp(): void
    {
        $this->parser->parseFilesAndDirectories([__DIR__ . '/Source']);
        $classReflection = $this->reflectionStorage->getClassReflections()[SomeClassWithAnnotations::class];
        $this->reflection = $classReflection->getMethod('methodWithArgs');
    }

    public function test(): void
    {
        $this->assertTrue($this->reflection->isPublic());
        $this->assertFalse($this->reflection->isProtected());
        $this->assertFalse($this->reflection->isPrivate());
    }
}
