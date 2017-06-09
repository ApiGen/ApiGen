<?php declare(strict_types=1);

namespace ApiGen\DependencyInjection;

use ApiGen\DependencyInjection\CompilerPass\CollectorCompilerPass;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;

final class AppKernel extends Kernel
{
    public function __construct()
    {
        parent::__construct('dev',true);
    }

    /**
     * @return string[]
     */
    public function registerBundles(): array
    {
        return [];
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(__DIR__ . '/../config/services.yml');
    }

    public function getCacheDir(): string
    {
        return sys_get_temp_dir();
    }

    protected function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new CollectorCompilerPass());
    }
}