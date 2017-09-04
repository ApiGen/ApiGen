<?php declare(strict_types=1);

namespace ApiGen\Element\Tests\ReflectionCollector;

use ApiGen\Annotation\AnnotationList;
use ApiGen\Element\ReflectionCollector\AnnotationReflectionCollector;
use ApiGen\ModularConfiguration\Option\AnnotationGroupsOption;
use ApiGen\ModularConfiguration\Option\DestinationOption;
use ApiGen\ModularConfiguration\Option\SourceOption;
use ApiGen\Tests\AbstractParserAwareTestCase;

final class AnnotationReflectionCollectorTest extends AbstractParserAwareTestCase
{
    /**
     * @var AnnotationReflectionCollector
     */
    private $annotationReflectionCollector;

    protected function setUp(): void
    {
        $this->configuration->resolveOptions([
           SourceOption::NAME => [__DIR__ . '/Source'],
           DestinationOption::NAME => TEMP_DIR,
           AnnotationGroupsOption::NAME => [AnnotationList::DEPRECATED],
        ]);

        $this->parser->parse();

        $this->annotationReflectionCollector = $this->container->get(AnnotationReflectionCollector::class);
    }

    public function test(): void
    {
        $this->assertCount(
            1,
            $this->annotationReflectionCollector->getClassReflections(AnnotationList::DEPRECATED)
        );
        $this->assertCount(
            1,
            $this->annotationReflectionCollector->getInterfaceReflections(AnnotationList::DEPRECATED)
        );
        $this->assertCount(
            1,
            $this->annotationReflectionCollector->getTraitReflections(AnnotationList::DEPRECATED)
        );
        $this->assertCount(
            1,
            $this->annotationReflectionCollector->getFunctionReflections(AnnotationList::DEPRECATED)
        );
        $this->assertCount(
            1,
            $this->annotationReflectionCollector->getClassOrTraitMethodReflections(AnnotationList::DEPRECATED)
        );
        $this->assertCount(
            1,
            $this->annotationReflectionCollector->getClassOrTraitPropertyReflections(AnnotationList::DEPRECATED)
        );
        $this->assertCount(
            1,
            $this->annotationReflectionCollector->getClassOrInterfaceConstantReflections(AnnotationList::DEPRECATED)
        );
    }
}
