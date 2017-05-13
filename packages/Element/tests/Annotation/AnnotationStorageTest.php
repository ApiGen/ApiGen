<?php declare(strict_types=1);

namespace ApiGen\Element\Tests\Annotation;

use ApiGen\Annotation\AnnotationList;
use ApiGen\Contracts\Configuration\ConfigurationInterface;
use ApiGen\Element\Annotation\AnnotationStorage;
use ApiGen\ModularConfiguration\Option\AnnotationGroupsOption;
use ApiGen\ModularConfiguration\Option\DestinationOption;
use ApiGen\Reflection\Contract\ParserInterface;
use ApiGen\Tests\AbstractContainerAwareTestCase;

final class AnnotationStorageTest extends AbstractContainerAwareTestCase
{
    /**
     * @var AnnotationStorage
     */
    private $annotationStorage;

    protected function setUp(): void
    {
        /** @var ConfigurationInterface $configuration */
        $configuration = $this->container->getByType(ConfigurationInterface::class);
        $configuration->resolveOptions([
           DestinationOption::NAME => '...',
           AnnotationGroupsOption::NAME => [AnnotationList::DEPRECATED]
        ]);

        /** @var ParserInterface $parser */
        $parser = $this->container->getByType(ParserInterface::class);
        $parser->parseDirectories([__DIR__ . '/Source']);

        $this->annotationStorage = $this->container->getByType(AnnotationStorage::class);
    }

    public function test(): void
    {
        $singleAnnotationStorage = $this->annotationStorage->findByAnnotation(AnnotationList::DEPRECATED);
        $this->assertCount(1, $singleAnnotationStorage->getClassReflections());
        $this->assertCount(0, $singleAnnotationStorage->getInterfaceReflections());
        $this->assertCount(0, $singleAnnotationStorage->getTraitReflections());
        $this->assertCount(1, $singleAnnotationStorage->getFunctionReflections());
        $this->assertCount(1, $singleAnnotationStorage->getClassOrTraitMethodReflections());
        $this->assertCount(1, $singleAnnotationStorage->getClassOrTraitPropertyReflections());
        // @todo 1
        $this->assertCount(0, $singleAnnotationStorage->getClassOrInterfaceConstantReflections());
    }
}
