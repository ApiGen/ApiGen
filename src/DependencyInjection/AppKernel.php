<?php declare(strict_types=1);

namespace ApiGen\DependencyInjection;

use ApiGen\DependencyInjection\CompilerPass\CollectorCompilerPass;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;

final class AppKernel extends Kernel
{
    /**
     * @var string
     */
    private const CONFIG_NAME = 'apigen.yml';

    public function __construct()
    {
        parent::__construct('dev', true);
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(__DIR__ . '/../config/services.yml');

        $localConfig = getcwd() . '/' . self::CONFIG_NAME;
        if (file_exists($localConfig)) {
            $loader->load($localConfig);
        }
    }

    /**
     * @return string[]
     */
    public function registerBundles(): array
    {
        return [];
    }

    public function getCacheDir(): string
    {
        return sys_get_temp_dir() . '/_apigen_kernel_cache';
    }

    public function getLogDir(): string
    {
        return sys_get_temp_dir() . '/_apigen_kernel_log';
    }

    protected function build(ContainerBuilder $containerBuilder): void
    {
        $containerBuilder->addCompilerPass(new CollectorCompilerPass);
    }
}
