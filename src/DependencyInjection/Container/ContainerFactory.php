<?php declare(strict_types=1);

namespace ApiGen\DependencyInjection\Container;

use ApiGen\DependencyInjection\AppKernel;
use Symfony\Component\DependencyInjection\Container;

final class ContainerFactory
{
    /**
     * @return PsrContainerInterface
     */
    public function create(): Container
    {
        $kernel = new AppKernel;
        $kernel->boot();

        return $kernel->getContainer();
    }
}
