<?php declare(strict_types=1);

namespace ApiGen\Tests;

use ApiGen\Configuration\ConfigurationOptions;
use ApiGen\Contracts\Configuration\ConfigurationInterface;
use ApiGen\DI\Container\ContainerFactory;
use Nette\DI\Container;
use PHPUnit\Framework\TestCase;

abstract class AbstractContainerAwareTestCase extends TestCase
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * @var string
     */
    protected $sourceDir;

    /**
     * @var string
     */
    protected $destinationDir;

    /**
     * @param string $name
     * @param mixed[] $data
     * @param string $dataName
     */
    public function __construct(?string $name = null, array $data = [], string $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->container = (new ContainerFactory)->create();

        $this->sourceDir = $this->container->getParameters()['appDir'] . '/Project';
        $this->destinationDir = $this->container->getParameters()['tempDir'] . '/api';

        $configuration = $this->container->getByType(ConfigurationInterface::class);
        $configuration->resolveOptions([
            ConfigurationOptions::SOURCE => [__DIR__],
            ConfigurationOptions::DESTINATION => TEMP_DIR,
        ]);
    }
}
