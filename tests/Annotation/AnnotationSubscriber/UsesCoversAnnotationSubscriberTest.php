<?php declare(strict_types=1);

namespace ApiGen\Tests\Annotation\AnnotationSubscriber;

use ApiGen\Annotation\AnnotationDecorator;
use ApiGen\Annotation\AnnotationList;
use ApiGen\Reflection\Contract\Reflection\Class_\ClassReflectionInterface;
use ApiGen\Tests\AbstractParserAwareTestCase;
use ApiGen\Tests\Annotation\AnnotationSubscriber\Source\UsesCoversClass;

final class UsesCoversAnnotationSubscriberTest extends AbstractParserAwareTestCase
{
    /**
     * @var AnnotationDecorator
     */
    private $annotationDecorator;

    /**
     * @var ClassReflectionInterface
     */
    private $classReflection;

    protected function setUp(): void
    {
        $this->parser->parseDirectories([__DIR__ . '/Source']);
        $this->annotationDecorator = $this->container->getByType(AnnotationDecorator::class);

        $this->classReflection = $this->reflectionStorage->getClassReflections()[UsesCoversClass::class];
    }

    public function testUses(): void
    {
        $usesAnnotation = $this->classReflection->getAnnotation(AnnotationList::USES)[0];
        $this->assertSame(
            '<code>ContainerBuilder</code>',
            $this->annotationDecorator->decorate($usesAnnotation, $this->classReflection)
        );
    }

    public function testUsesExistingClass(): void
    {
        $usesAnnotation = $this->classReflection->getAnnotation(AnnotationList::USES)[1];
        $this->assertSame(
            '<code><a href="class-ApiGen.Tests.Annotation.AnnotationSubscriber.Source.UsedClass.html">UsedClass</a></code>',
            $this->annotationDecorator->decorate($usesAnnotation, $this->classReflection)
        );
    }

    public function testCovers(): void
    {
        $coversAnnotation = $this->classReflection->getAnnotation(AnnotationList::COVERS)[0];
        $this->assertSame(
            '<code>ContainerBuilder::getByType()</code>',
            $this->annotationDecorator->decorate($coversAnnotation, $this->classReflection)
        );
    }
}
