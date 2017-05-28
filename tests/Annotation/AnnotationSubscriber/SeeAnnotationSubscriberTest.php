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
        $decoratedDescription = $this->annotationDecorator->decorate($this->classReflection->getDescription(), $this->classReflection);

        $this->assertSame(
            'User session storage for PHP < 5.4.'
                . PHP_EOL
                . PHP_EOL
                . ' <a href="http://php.net/session_set_save_handler">http://php.net/session_set_save_handler</a>',
            $decoratedDescription
        );
    }
}
