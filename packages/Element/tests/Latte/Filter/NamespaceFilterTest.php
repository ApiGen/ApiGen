<?php declare(strict_types=1);

namespace ApiGen\Element\Tests\Latte\Filter;

use ApiGen\Element\Latte\Filter\NamespaceFilter;
use ApiGen\Tests\AbstractContainerAwareTestCase;

final class NamespaceFilterTest extends AbstractContainerAwareTestCase
{
    public function test(): void
    {
        $namespaceFilter = $this->container->getByType(NamespaceFilter::class);
        $filter = $namespaceFilter->getFilters()['subNamespaceName'];

        $this->assertSame('SubNamespace', $filter('SubNamespace'));
        $this->assertSame('SubNamespace', $filter('Namespace\SubNamespace'));
        $this->assertSame('OneMore', $filter('Namespace\SubNamespace\OneMore'));
    }
}
