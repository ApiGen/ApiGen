<?php declare(strict_types=1);

namespace ApiGen\Element\Tests\Latte\Filter;

use ApiGen\Element\Latte\Filter\NamespaceFilter;
use ApiGen\Tests\AbstractContainerAwareTestCase;

final class NamespaceFilterTest extends AbstractContainerAwareTestCase
{
    /**
     * @var NamespaceFilter
     */
    private $namespaceFilter;

    protected function setUp(): void
    {
        $this->namespaceFilter = $this->container->getByType(NamespaceFilter::class);
    }

    public function testSubNamespace(): void
    {
        $filter = $this->namespaceFilter->getFilters()['subNamespace'];

        $this->assertSame('SubNamespace', $filter('SubNamespace'));
        $this->assertSame('SubNamespace', $filter('Namespace\SubNamespace'));
        $this->assertSame('OneMore', $filter('Namespace\SubNamespace\OneMore'));
    }

    public function testLinkAllNamespaceParts(): void
    {
        $filter = $this->namespaceFilter->getFilters()['linkAllNamespaceParts'];
        $this->assertSame(
            '<a href="namespace-Long.html">Long</a>\<a href="namespace-Long-Namespace.html">Namespace</a>',
            $filter('Long\Namespace')
        );
    }
}
