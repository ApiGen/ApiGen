<?php declare(strict_types=1);

namespace ApiGen\Tests\Annotation\AnnotationSubscriber;

use ApiGen\Annotation\AnnotationDecorator;
use ApiGen\Annotation\AnnotationList;
use ApiGen\Reflection\Contract\Reflection\Class_\ClassMethodReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Class_\ClassReflectionInterface;
use ApiGen\Tests\AbstractParserAwareTestCase;
use ApiGen\Tests\Annotation\AnnotationSubscriber\SeeAnnotationSubscriberSource\SomeClassWithSeeAnnotations;

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
        $this->parser->parseDirectories([__DIR__ . '/SeeAnnotationSubscriberSource']);
        $this->annotationDecorator = $this->container->getByType(AnnotationDecorator::class);

        $this->classReflection = $this->reflectionStorage->getClassReflections()[SomeClassWithSeeAnnotations::class];
        $this->methodReflection = $this->classReflection->getMethod('returnArray');
    }

    public function testPropertyOnMissingClassReflection()
    {
        $seePropertyAnnotation = $this->methodReflection->getAnnotation(AnnotationList::SEE)[0];

        $this->assertSame(
            '<code>ReturnedClass::$someProperty</code>',
            $this->annotationDecorator->decorate($seePropertyAnnotation, $this->methodReflection)
        );
    }

    public function testProperty()
    {
        $seePropertyAnnotation = $this->methodReflection->getAnnotation(AnnotationList::SEE)[2];

        $this->assertSame(
            '<code><a href="class-ApiGen.Tests.Annotation.AnnotationSubscriber.SeeAnnotationSubscriberSource.PresentReturnedClass.html#$someProperty">PresentReturnedClass::$someProperty</a></code>',
            $this->annotationDecorator->decorate($seePropertyAnnotation, $this->methodReflection)
        );
    }

    public function testMethodOnMissingClassReflection()
    {
        $seeMethodAnnotation = $this->methodReflection->getAnnotation(AnnotationList::SEE)[1];

        $this->assertSame(
            '<code>ReturnedClass::someMethod()</code>',
            $this->annotationDecorator->decorate($seeMethodAnnotation, $this->methodReflection)
        );
    }

    public function testMethod()
    {
        $seeMethodAnnotation = $this->methodReflection->getAnnotation(AnnotationList::SEE)[3];

        $this->assertSame(
            '<code><a href="class-ApiGen.Tests.Annotation.AnnotationSubscriber.SeeAnnotationSubscriberSource.PresentReturnedClass.html#_someMethod">PresentReturnedClass::someMethod()</a></code>',
            $this->annotationDecorator->decorate($seeMethodAnnotation, $this->methodReflection)
        );
    }

    public function testMissingFunction(): void
    {
    }

    public function testFunction(): void
    {
    }

//['{@see NotActiveClass}', 'NotActiveClass'],
//[sprintf('{@see %s}', GenerateCommand::class), self::APIGEN_LINK],

}
