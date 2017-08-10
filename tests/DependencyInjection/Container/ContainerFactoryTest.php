<?php declare(strict_types=1);

namespace ApiGen\Tests\DependencyInjection\Container;

use ApiGen\Application\ApiGenApplication;
use ApiGen\DependencyInjection\Container\ContainerFactory;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Container;

final class ContainerFactoryTest extends TestCase
{
    /**
     * @var Container
     */
    private $container;

    protected function setUp(): void
    {
        $this->container = (new ContainerFactory)->create();
    }

    public function test(): void
    {
        $this->assertInstanceOf(Container::class, $this->container);
        $this->assertInstanceOf(ApiGenApplication::class, $this->container->get(ApiGenApplication::class));
    }
}
