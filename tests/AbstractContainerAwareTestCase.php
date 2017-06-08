<?php declare(strict_types=1);

namespace ApiGen\Tests;

use ApiGen\Configuration\Configuration;
//use ApiGen\DependencyInjection\Container\ContainerFactory;
use ApiGen\DependencyInjection\Container\ContainerFactory;
use ApiGen\ModularConfiguration\Option\DestinationOption;
use ApiGen\ModularConfiguration\Option\SourceOption;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

abstract class AbstractContainerAwareTestCase extends TestCase
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @param mixed[] $data
     */
    public function __construct(?string $name = null, array $data = [], string $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->container = (new ContainerFactory)->create();

        /** @var Configuration $configuration */
        $configuration = $this->container->get(Configuration::class);
        $configuration->resolveOptions([
            SourceOption::NAME => [__DIR__],
            DestinationOption::NAME => TEMP_DIR,
        ]);
    }
}
