<?php declare(strict_types=1);

namespace ApiGen\Element\Tests\ReflectionCollector;

use ApiGen\Annotation\AnnotationList;
use ApiGen\Contracts\Configuration\ConfigurationInterface;
use ApiGen\Element\ReflectionCollector\AnnotationReflectionCollector;
use ApiGen\ModularConfiguration\Option\AnnotationGroupsOption;
use ApiGen\ModularConfiguration\Option\DestinationOption;
use ApiGen\Reflection\Contract\ParserInterface;
use ApiGen\Tests\AbstractContainerAwareTestCase;

final class AnnotationReflectionCollectorTest extends AbstractContainerAwareTestCase
{
    /**
     * @var AnnotationReflectionCollector
     */
    private $annotationReflectionCollector;

    protected function setUp(): void
    {
        /** @var ConfigurationInterface $configuration */
        $configuration = $this->container->getByType(ConfigurationInterface::class);
        $configuration->resolveOptions([
           DestinationOption::NAME => TEMP_DIR,
           AnnotationGroupsOption::NAME => [AnnotationList::DEPRECATED]
        ]);

        /** @var ParserInterface $parser */
        $parser = $this->container->getByType(ParserInterface::class);
        $parser->parseDirectories([__DIR__ . '/Source']);

        $this->annotationReflectionCollector = $this->container->getByType(AnnotationReflectionCollector::class);
    }

    public function test(): void
    {
        $this->annotationReflectionCollector->setActiveAnnotation(AnnotationList::DEPRECATED);

        $this->assertCount(1, $this->annotationReflectionCollector->getClassReflections());
        $this->assertCount(1, $this->annotationReflectionCollector->getInterfaceReflections());
        $this->assertCount(1, $this->annotationReflectionCollector->getTraitReflections());
        $this->assertCount(1, $this->annotationReflectionCollector->getFunctionReflections());
        $this->assertCount(1, $this->annotationReflectionCollector->getClassOrTraitMethodReflections());
        $this->assertCount(1, $this->annotationReflectionCollector->getClassOrTraitPropertyReflections());
        // @todo 1
        $this->assertCount(0, $this->annotationReflectionCollector->getClassOrInterfaceConstantReflections());
    }
}
