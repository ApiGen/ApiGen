<?php declare(strict_types=1);

namespace ApiGen\DependencyInjection\Container;

use ApiGen\DependencyInjection\AppKernel;
use Psr\Container\ContainerInterface;

final class ContainerFactory
{
    public function create(): ContainerInterface
    {
        $kernel = new AppKernel;
        $kernel->boot();

        return $kernel->getContainer();
    }
}
