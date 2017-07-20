<?php declare(strict_types=1);

namespace ApiGen\Annotation\Tests\AnnotationSubscriber;

use ApiGen\Annotation\AnnotationDecorator;
use ApiGen\Annotation\AnnotationList;
use ApiGen\Annotation\Tests\AnnotationSubscriber\Source\UsesCoversClass;
use ApiGen\Reflection\Contract\Reflection\Class_\ClassReflectionInterface;
use ApiGen\Tests\AbstractParserAwareTestCase;

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
        $this->parser->parseFilesAndDirectories([__DIR__ . '/Source']);
        $this->annotationDecorator = $this->container->get(AnnotationDecorator::class);

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
            '<code><a href="class-ApiGen.Annotation.Tests.AnnotationSubscriber.Source.UsedClass.html">'
            . 'UsedClass</a></code>',
            $this->annotationDecorator->decorate($usesAnnotation, $this->classReflection)
        );
    }

    public function testCovers(): void
    {
        $coversAnnotation = $this->classReflection->getAnnotation(AnnotationList::COVERS)[0];
        $this->assertSame(
            '<code>ContainerBuilder::get()</code>',
            $this->annotationDecorator->decorate($coversAnnotation, $this->classReflection)
        );
    }
}
