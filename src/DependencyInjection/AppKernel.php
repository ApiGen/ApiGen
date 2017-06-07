<?php declare(strict_types=1);

namespace ApiGen\DependencyInjection;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;

final class AppKernel extends Kernel
{
    public function __construct()
    {
        parent::__construct('dev',true);
    }

    public function registerBundles(): array
    {
        return [];
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(__DIR__ . '/../config/services.yaml');
    }

    protected function build(ContainerBuilder $container): void
    {
    }
}
