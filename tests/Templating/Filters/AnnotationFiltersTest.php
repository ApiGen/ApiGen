<?php declare(strict_types=1);

namespace ApiGen\Tests\Templating\Filters;

use ApiGen\Configuration\ConfigurationOptions as CO;
use ApiGen\Contracts\Configuration\ConfigurationInterface;
use ApiGen\Templating\Filters\AnnotationFilters;
use PHPUnit\Framework\TestCase;

final class AnnotationFiltersTest extends TestCase
{
    /**
     * @var AnnotationFilters
     */
    private $annotationFilters;

    protected function setUp(): void
    {
        $configurationMock = $this->createMock(ConfigurationInterface::class);
        $configurationMock->method('getOption')
            ->with(CO::INTERNAL)
            ->willReturn(false);

        $this->annotationFilters = new AnnotationFilters($configurationMock);
    }

    public function testAnnotationBeautify(): void
    {
        $this->assertSame('Method', $this->annotationFilters->annotationBeautify('method'));
    }

    public function testAnnotationFilter(): void
    {
        $annotations = ['method' => true, 'remain' => true, 'internal' => true];
        $this->assertSame(
            ['remain' => true],
            $this->annotationFilters->annotationFilter($annotations)
        );
    }

    public function testAnnotationFilterWithCustom(): void
    {
        $annotations = ['remain' => true, 'otherToRemain' => true];
        $this->assertSame(
            ['otherToRemain' => true],
            $this->annotationFilters->annotationFilter($annotations, ['remain'])
        );
    }
}
