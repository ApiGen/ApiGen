<?php declare(strict_types=1);

namespace ApiGen\Tests\Templating\Filters;

use ApiGen\Reflection\Contract\Reflection\Class_\ClassReflectionInterface;
use ApiGen\Tests\AbstractContainerAwareTestCase;

final class NamespaceUrlFiltersTest extends AbstractContainerAwareTestCase
{
    public function testNamespaceLinks(): void
    {
        $this->assertSame(
            '<a href="namespace-Long.html">Long</a>\<a href="namespace-Long.Namespace.html">Namespace</a>',
            $this->namespaceUrlFilters->namespaceLinks('Long\Namespace')
        );
    }

    public function testNamespaceLinksWithNoNamespaces(): void
    {
        $this->assertSame(
            '<a href="namespace-Long.html">Long</a>\<a href="namespace-Long.Namespace.html">Namespace</a>',
            $this->namespaceUrlFilters->namespaceLinks('Long\\Namespace')
        );
    }
}
