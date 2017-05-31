<?php declare(strict_types=1);

namespace ApiGen\DI\Container;

use Nette\Configurator;
use Nette\DI\Container;
use Nette\DI\Extensions\ExtensionsExtension;
use Nette\Utils\FileSystem;

final class ContainerFactory
{
    /**
     * @var string
     */
    private const CONFIG_NAME = 'apigen.neon';

    public function create(): Container
    {
        $configurator = new Configurator;
        $configurator->setTempDirectory($this->createAndReturnTempDir());

        $this->loadConfigFiles($configurator);
        $this->setDefaultExtensions($configurator);

        return $configurator->createContainer();
    }

    private function createAndReturnTempDir(): string
    {
        $tempDir = sys_get_temp_dir() . '/_apigen';
        FileSystem::delete($tempDir);
        FileSystem::createDir($tempDir);
        return $tempDir;
    }

    private function loadConfigFiles(Configurator $configurator): void
    {
        $configurator->addConfig(__DIR__ . '/../../config/config.neon');
        $localConfig = getcwd() . '/' . self::CONFIG_NAME;

        if (file_exists($localConfig)) {
            $configurator->addConfig($localConfig);
        }
    }

    private function setDefaultExtensions(Configurator $configurator): void
    {
        $configurator->defaultExtensions = [
            'extensions' => ExtensionsExtension::class,
        ];
    }
}
