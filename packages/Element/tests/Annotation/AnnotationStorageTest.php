<?php declare(strict_types=1);

namespace ApiGen\Element\Tests\Annotation;

use ApiGen\Annotation\AnnotationList;
use ApiGen\Element\Annotation\AnnotationStorage;
use ApiGen\Tests\AbstractContainerAwareTestCase;

final class AnnotationStorageTest extends AbstractContainerAwareTestCase
{
    /**
     * @var AnnotationStorage
     */
    private $annotationStorage;

    protected function setUp(): void
    {
        $this->annotationStorage = $this->container->getByType(AnnotationStorage::class);
    }

    public function testExtractElementsByAnnotation(): void
    {
        $reflectionsWithDeprecatedAnnotation = $this->annotationStorage->findByAnnotation(AnnotationList::DEPRECATED);
        // @todo value object
        $this->assertCount(1, $reflectionsWithDeprecatedAnnotation['classes']);
    }
}
