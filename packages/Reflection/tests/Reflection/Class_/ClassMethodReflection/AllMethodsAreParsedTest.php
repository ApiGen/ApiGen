<?php declare(strict_types=1);

namespace ApiGen\Reflection\Tests\Reflection\Class_\ClassMethodReflection;

use ApiGen\Reflection\Tests\Reflection\Class_\ClassMethodReflection\Source\ClassWithManyMethods;
use ApiGen\Tests\AbstractParserAwareTestCase;

/**
 * @link https://github.com/ApiGen/ApiGen/issues/848
 */
final class AllMethodsAreParsedTest extends AbstractParserAwareTestCase
{
    public function test(): void
    {
        $this->parser->parseDirectories([__DIR__ . '/Source']);

        $classReflections = $this->reflectionStorage->getClassReflections();
        $classReflection = $classReflections[ClassWithManyMethods::class];
        $this->assertCount(12, $classReflection->getMethods());
    }
}
