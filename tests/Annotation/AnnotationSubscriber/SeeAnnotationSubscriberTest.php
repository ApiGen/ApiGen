<?php declare(strict_types=1);

namespace ApiGen\Tests\Annotation\AnnotationSubscriber;

use ApiGen\Annotation\AnnotationDecorator;
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

    protected function setUp(): void
    {
        $this->parser->parseDirectories([__DIR__ . '/SeeAnnotationSubscriberSource']);
        $this->annotationDecorator = $this->container->getByType(AnnotationDecorator::class);

        $this->classReflection = $this->reflectionStorage->getClassReflections()[SomeClassWithSeeAnnotations::class];
    }

    public function test(): void
    {
        dump($this->classReflection->getDescription());
        // use link
        die;
        // $this->
    }
}