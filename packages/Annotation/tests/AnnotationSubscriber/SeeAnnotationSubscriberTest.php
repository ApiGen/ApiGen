<?php declare(strict_types=1);

namespace ApiGen\Annotation\Tests\AnnotationSubscriber;

use ApiGen\Annotation\AnnotationDecorator;
use ApiGen\Annotation\AnnotationList;
use ApiGen\Annotation\Tests\AnnotationSubscriber\SeeAnnotationSubscriberSource\SomeClassWithSeeAnnotations;
use ApiGen\Reflection\Contract\Reflection\Class_\ClassMethodReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Class_\ClassReflectionInterface;
use ApiGen\Tests\AbstractParserAwareTestCase;

final class SeeAnnotationSubscriberTest extends AbstractParserAwareTestCase
{
    /**
     * @var AnnotationDecorator
     */
    private $annotationDecorator;

    /**
     * @var ClassReflectionInterface
     */
    private $classReflection;

    /**
     * @var ClassMethodReflectionInterface
     */
    private $methodReflection;

    protected function setUp(): void
    {
        $this->parser->parseFilesAndDirectories([__DIR__ . '/SeeAnnotationSubscriberSource']);
        $this->annotationDecorator = $this->container->get(AnnotationDecorator::class);

        $this->classReflection = $this->reflectionStorage->getClassReflections()[SomeClassWithSeeAnnotations::class];
        $this->methodReflection = $this->classReflection->getMethod('returnArray');
    }

    public function testPropertyOnMissingClassReflection(): void
    {
        $seePropertyAnnotation = $this->methodReflection->getAnnotation(AnnotationList::SEE)[0];

        $this->assertSame(
            '<code>ReturnedClass::$someProperty</code>',
            $this->annotationDecorator->decorate($seePropertyAnnotation, $this->methodReflection)
        );
    }

    public function testProperty(): void
    {
        $seePropertyAnnotation = $this->methodReflection->getAnnotation(AnnotationList::SEE)[2];

        $this->assertSame(
            '<code><a href="class-ApiGen.Annotation.Tests.AnnotationSubscriber.SeeAnnotationSubscriberSource.'
            . 'PresentReturnedClass.html#$someProperty">PresentReturnedClass::$someProperty</a></code>',
            $this->annotationDecorator->decorate($seePropertyAnnotation, $this->methodReflection)
        );
    }

    public function testMethodOnMissingClassReflection(): void
    {
        $seeMethodAnnotation = $this->methodReflection->getAnnotation(AnnotationList::SEE)[1];

        $this->assertSame(
            '<code>ReturnedClass::someMethod()</code>',
            $this->annotationDecorator->decorate($seeMethodAnnotation, $this->methodReflection)
        );
    }

    public function testMethod(): void
    {
        $seeMethodAnnotation = $this->methodReflection->getAnnotation(AnnotationList::SEE)[3];

        $this->assertSame(
            '<code><a href="class-ApiGen.Annotation.Tests.AnnotationSubscriber.SeeAnnotationSubscriberSource'
            . '.PresentReturnedClass.html#_someMethod">PresentReturnedClass::someMethod()</a></code>',
            $this->annotationDecorator->decorate($seeMethodAnnotation, $this->methodReflection)
        );
    }

    public function testMissingFunction(): void
    {
        $seeFunctionAnnotation = $this->methodReflection->getAnnotation(AnnotationList::SEE)[4];

        $this->assertSame(
            '<code>someMissingFunction()</code>',
            $this->annotationDecorator->decorate($seeFunctionAnnotation, $this->methodReflection)
        );
    }

    public function testFunction(): void
    {
        $seeFunctionAnnotation = $this->methodReflection->getAnnotation(AnnotationList::SEE)[5];

        $this->assertSame(
            '<code><a href="function-ApiGen.Annotation.Tests.AnnotationSubscriber.SeeAnnotationSubscriberSource'
            . '.someExistingFunction.html">someExistingFunction()</a></code>',
            $this->annotationDecorator->decorate($seeFunctionAnnotation, $this->methodReflection)
        );
    }
}
