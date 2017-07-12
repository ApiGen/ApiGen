<?php declare(strict_types=1);

namespace ApiGen\Annotation\Tests\Latte\Filter;

use ApiGen\Annotation\Latte\Filter\AnnotationFilterProvider;
use ApiGen\Annotation\Tests\Latte\Filter\AnnotationFilterProviderSource\ClassWithAnnotations;
use ApiGen\Reflection\Contract\Reflection\Class_\ClassReflectionInterface;
use ApiGen\Tests\AbstractParserAwareTestCase;

final class AnnotationFilterProviderTest extends AbstractParserAwareTestCase
{
    /**
     * @var AnnotationFilterProvider
     */
    private $annotationFilterProvider;

    /**
     * @var ClassReflectionInterface
     */
    private $classReflection;

    protected function setUp(): void
    {
        $this->annotationFilterProvider = $this->container->get(AnnotationFilterProvider::class);
        $this->parser->parseFilesAndDirectories([__DIR__ . '/AnnotationFilterProviderSource']);
        $this->classReflection = $this->reflectionStorage->getClassReflections()[ClassWithAnnotations::class];
    }

    public function test(): void
    {
        $annotations = $this->classReflection->getAnnotations();
        $this->assertCount(2, $annotations);

        $annotationFilter = $this->annotationFilterProvider->getFilters()['annotationFilter'];
        $this->assertCount(1, $annotationFilter($annotations, ['see']));
        $this->assertCount(1, $annotationFilter($annotations, ['var']));
        $this->assertCount(2, $annotationFilter($annotations, ['invalid']));
    }
}
