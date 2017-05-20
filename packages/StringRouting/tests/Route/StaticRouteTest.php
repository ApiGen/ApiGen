<?php declare(strict_types=1);

namespace ApiGen\StringRouting\Tests\Route;

use ApiGen\StringRouting\Route\NamespaceRoute;
use ApiGen\StringRouting\Route\StaticRoute;
use ApiGen\StringRouting\StringRouter;
use ApiGen\Tests\AbstractContainerAwareTestCase;

final class StaticRouteTest extends AbstractContainerAwareTestCase
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
        $file = __DIR__ . '/Source/someFile.txt';

        $this->assertSame(
            '/',
            $this->stringRouter->buildRoute(StaticRoute::NAME, $file)
        );
    }
}
