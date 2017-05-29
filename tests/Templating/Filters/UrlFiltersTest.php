<?php declare(strict_types=1);

namespace ApiGen\Tests\Templating\Filters;

use ApiGen\Reflection\Contract\Reflection\Class_\ClassReflectionInterface;
use ApiGen\Tests\AbstractContainerAwareTestCase;

final class UrlFiltersTest extends AbstractContainerAwareTestCase
{
    public function testHighlightPhp(): void
    {
        $reflectionClassMock = $this->createMock(ClassReflectionInterface::class);
        $this->assertSame(
            '<span class="php-keyword1">echo</span> <span class="php-quote">&quot;hi&quot;</span>;',
            $this->urlFilters->highlightPhp('echo "hi";', $reflectionClassMock)
        );
    }

    public function testHighlightValue(): void
    {
        $reflectionClassMock = $this->createMock(ClassReflectionInterface::class);
        $this->assertSame(
            '<span class="php-var">$var</span> = <span class="php-num">5</span>;',
            $this->urlFilters->highlightValue('$var = 5;', $reflectionClassMock)
        );
    }
}
