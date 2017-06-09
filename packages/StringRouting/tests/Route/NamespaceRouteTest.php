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
        $this->stringRouter = $this->container->get(StringRouter::class);
    }

    public function test(): void
    {
        $namespaceName = 'SomeNamespace\SubNamespace';

        $this->assertSame(
            'namespace-SomeNamespace.SubNamespace.html',
            $this->stringRouter->buildRoute(NamespaceRoute::NAME, $namespaceName)
        );
    }
}
