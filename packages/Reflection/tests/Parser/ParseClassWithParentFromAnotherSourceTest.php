<?php declare(strict_types=1);

namespace ApiGen\Reflection\Tests\Parser;

use ApiGen\Reflection\Contract\Reflection\Class_\ClassReflectionInterface;
use ApiGen\Reflection\Tests\Parser\AnotherSource\ParentClassFromAnotherSource;
use ApiGen\Reflection\Tests\Parser\Source\ClassWithParentFromAnotherSource;
use ApiGen\Tests\AbstractParserAwareTestCase;

final class ParseClassWithParentFromAnotherSourceTest extends AbstractParserAwareTestCase
{
    public function test(): void
    {
        $this->resolveConfigurationBySource([__DIR__ . '/Source']);
        $this->parser->parse();

        $classReflections = $this->reflectionStorage->getClassReflections();
        $this->assertCount(2, $classReflections);

        $classReflection = $classReflections[ClassWithParentFromAnotherSource::class];

        $parentClassReflection = $classReflection->getParentClass();
        $this->assertInstanceOf(ClassReflectionInterface::class, $parentClassReflection);
        $this->assertSame(ParentClassFromAnotherSource::class, $parentClassReflection->getName());
    }
}
