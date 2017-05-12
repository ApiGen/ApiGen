<?php declare(strict_types=1);

namespace ApiGen\Element\Tests\Annotation;

use ApiGen\Annotation\AnnotationList;
use ApiGen\Element\Annotation\AnnotationStorage;
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
        /** @var ParserInterface $parser */
        $parser = $this->container->getByType(ParserInterface::class);
        $parser->parseDirectories([__DIR__ . '/Source']);

        $this->annotationStorage = $this->container->getByType(AnnotationStorage::class);
    }

    public function test(): void
    {
        $reflectionsWithDeprecatedAnnotation = $this->annotationStorage->findByAnnotation(AnnotationList::DEPRECATED);
        // @todo value object
        $this->assertCount(1, $reflectionsWithDeprecatedAnnotation['classes']);
        $this->assertCount(1, $reflectionsWithDeprecatedAnnotation['interfaces']);
        $this->assertCount(1, $reflectionsWithDeprecatedAnnotation['traits']);
        $this->assertCount(1, $reflectionsWithDeprecatedAnnotation['functions']);
        $this->assertCount(1, $reflectionsWithDeprecatedAnnotation['methods']);
        $this->assertCount(1, $reflectionsWithDeprecatedAnnotation['properties']);
        $this->assertCount(1, $reflectionsWithDeprecatedAnnotation['constants']);
    }
}
