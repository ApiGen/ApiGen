<?php declare(strict_types=1);

namespace ApiGen\Reflection\Tests\Parser;

use ApiGen\Reflection\Tests\Parser\AnotherSource\ParentClassFromAnotherSource;
use ApiGen\Reflection\Tests\Parser\Source\ClassWithParentFromAnotherSource;
use ApiGen\Tests\AbstractParserAwareTestCase;
use Roave\BetterReflection\Reflection\ReflectionClass;
use Roave\BetterReflection\Reflector\Exception\IdentifierNotFound;

final class ParseClassWithParentFromAnotherSourceTest extends AbstractParserAwareTestCase
{
    public function test(): void
    {
        $this->parser->parseDirectories([__DIR__ . '/Source']);

        $classReflections = $this->reflectionStorage->getClassReflections();
        $this->assertCount(1, $classReflections);

        $classReflection = $classReflections[ClassWithParentFromAnotherSource::class];

        // @question: how to remove this and actually return parent class reflection?
        $this->expectException(IdentifierNotFound::class);
        $this->expectExceptionMessage(sprintf(
            '%s "%s" could not be found in the located source',
            ReflectionClass::class,
            ParentClassFromAnotherSource::class
        ));
        $classReflection->getParentClass();
    }
}
