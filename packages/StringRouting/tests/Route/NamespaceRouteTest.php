<?php declare(strict_types=1);

namespace ApiGen\StringRouting\Tests\Route;

use ApiGen\StringRouting\Route\NamespaceRoute;
use ApiGen\StringRouting\StringRouter;
use ApiGen\Tests\AbstractContainerAwareTestCase;

final class NamespaceRouteTest extends AbstractContainerAwareTestCase
{
    /**
     * @var StringRouter
     */
    private $stringRouter;

    protected function setUp(): void
    {
        $this->stringRouter = $this->container->getByType(StringRouter::class);
    }

    public function test(): void
    {
        $namespaceName = 'SomeNamespace\SubNamespace';

        $this->assertSame(
            'namespace-SomeNamespace-SubNamespace.html',
            $this->stringRouter->buildRoute(NamespaceRoute::NAME, $namespaceName)
        );
    }

//    public function testNamespaceLinks(): void
//    {
//        $this->assertSame(
//            '<a href="namespace-Long.html">Long</a>\<a href="namespace-Long.Namespace.html">Namespace</a>',
//            $this->namespaceUrlFilters->namespaceLinks('Long\Namespace')
//        );
//    }
//
//    public function testNamespaceLinksWithNoNamespaces(): void
//    {
//        $this->assertSame(
//            '<a href="namespace-Long.html">Long</a>\<a href="namespace-Long.Namespace.html">Namespace</a>',
//            $this->namespaceUrlFilters->namespaceLinks('Long\\Namespace')
//        );
//    }
//    {
//        $this->assertSame('Subgroup', $this->namespaceUrlFilters->subNamespaceName('Group\\Subgroup'));
//        $this->assertSame('Group', $this->namespaceUrlFilters->subNamespaceName('Group'));
//    }
}
