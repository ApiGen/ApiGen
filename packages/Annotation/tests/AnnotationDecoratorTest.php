<?php declare(strict_types=1);

namespace ApiGen\Annotation\Tests;

use ApiGen\Annotation\AnnotationDecorator;
use ApiGen\Annotation\AnnotationList;
use ApiGen\Annotation\Tests\AnnotationDecoratorSource\SomeClassWithReturnTypes;
use ApiGen\Reflection\Contract\Reflection\Class_\ClassMethodReflectionInterface;
use ApiGen\Tests\AbstractParserAwareTestCase;

final class AnnotationDecoratorTest extends AbstractParserAwareTestCase
{
    /**
     * @var string
     */
    private const URL = 'class-ApiGen.Annotation.Tests.AnnotationDecoratorSource.ReturnedClass.html';

    /**
     * @var AnnotationDecorator
     */
    private $annotationDecorator;

    /**
     * @var ClassMethodReflectionInterface
     */
    private $methodReflection;

    /**
     * @var ClassMethodReflectionInterface
     */
    private $secondMethodReflection;

    protected function setUp(): void
    {
        $this->parser->parseFilesAndDirectories([__DIR__ . '/AnnotationDecoratorSource']);
        $this->annotationDecorator = $this->container->get(AnnotationDecorator::class);

        $classReflection = $this->reflectionStorage->getClassReflections()[SomeClassWithReturnTypes::class];
        $this->methodReflection = $classReflection->getOwnMethods()['returnArray'];
        $this->secondMethodReflection = $classReflection->getOwnMethods()['returnClass'];
    }

    public function testClassArray(): void
    {
        $returnAnnotation = $this->methodReflection->getAnnotation(AnnotationList::RETURN_)[0];

        $this->assertSame(
            '<code><a href="' . self::URL . '">ReturnedClass</a>[]</code>',
            $this->annotationDecorator->decorate($returnAnnotation, $this->methodReflection)
        );
    }

    public function testReturnClass(): void
    {
        $returnAnnotation = $this->secondMethodReflection->getAnnotation(AnnotationList::RETURN_)[0];

        $this->assertSame(
            '<code><a href="' . self::URL . '">ReturnedClass</a></code>',
            $this->annotationDecorator->decorate($returnAnnotation, $this->methodReflection)
        );
    }

    public function testDoubleTypes(): void
    {
        $param1Annotation = $this->methodReflection->getAnnotation(AnnotationList::PARAM)[0];

        $this->assertSame(
            'int|string[]',
            $this->annotationDecorator->decorate($param1Annotation, $this->methodReflection)
        );
    }

    public function testDoubleWithSelfReference(): void
    {
        $param2Annotation = $this->methodReflection->getAnnotation(AnnotationList::PARAM)[1];

        // @todo it doesn't make sense to link itself here, since it's the same page
        $this->assertSame(
            'string|<code><a href="class-ApiGen.Annotation.Tests.AnnotationDecoratorSource.'
            . 'SomeClassWithReturnTypes.html">$this</a></code>',
            $this->annotationDecorator->decorate($param2Annotation, $this->methodReflection)
        );
    }
}
