<?php declare(strict_types=1);

namespace ApiGen\Annotation\Tests\AnnotationSubscriber;

use ApiGen\Annotation\AnnotationList;
use ApiGen\Annotation\Tests\AnnotationSubscriber\ResolvePropertySource\SelfPropertyClass;
use ApiGen\Reflection\Contract\Reflection\Class_\ClassMethodReflectionInterface;
use ApiGen\Tests\AbstractParserAwareTestCase;
use phpDocumentor\Reflection\DocBlock\Tags\See;

final class ResolvePropertyTest extends AbstractParserAwareTestCase
{
    /**
     * @var ClassMethodReflectionInterface
     */
    private $methodReflection;

    protected function setUp(): void
    {
        $this->parser->parseDirectories([__DIR__ . '/ResolvePropertySource']);

        $classReflection = $this->reflectionStorage->getClassReflections()[SelfPropertyClass::class];
        $this->methodReflection = $classReflection->getMethod('someMethod');
    }

    public function test(): void
    {
        /** @var See[] $seeAnnotations */
        $seeAnnotations = $this->methodReflection->getAnnotation(AnnotationList::SEE);
        $this->assertCount(1, $seeAnnotations);

        $seeAnnotation = $seeAnnotations[0];
        $this->assertSame(
            SelfPropertyClass::class . '::$someProperty',
            ltrim((string) $seeAnnotation->getReference(), '\\')
        );
    }
}
