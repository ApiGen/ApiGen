<?php declare(strict_types=1);

namespace ApiGen\Tests\Annotation;

use ApiGen\Annotation\AnnotationDecorator;
use ApiGen\Reflection\Contract\Reflection\Class_\ClassMethodReflectionInterface;
use ApiGen\Tests\AbstractContainerAwareTestCase;
use ApiGen\Tests\AbstractParserAwareTestCase;
use ApiGen\Tests\Annotation\AnnotationDecoratorSource\SomeClassWithReturnTypes;

final class AnnotationDecoratorTest extends AbstractParserAwareTestCase
{
    /**
     * @var AnnotationDecorator
     */
    private $annotationDecorator;

    /**
     * @var ClassMethodReflectionInterface
     */
    private $methodReflection;

    protected function setUp(): void
    {
        $this->parser->parseDirectories([__DIR__ . '/AnnotationDecoratorSource']);
        $this->annotationDecorator = $this->container->getByType(AnnotationDecorator::class);

        $classReflection = $this->reflectionStorage->getClassReflections()[SomeClassWithReturnTypes::class];
        $this->methodReflection = $classReflection->getOwnMethods()['returnArray'];
    }

    public function test(): void
    {
        dump($this->methodReflection->getAnnotations());
        die;

        $this->annotationDecorator->decorate();
    }
}
