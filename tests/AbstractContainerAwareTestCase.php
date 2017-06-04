<?php declare(strict_types=1);

namespace ApiGen\Tests;

use ApiGen\Configuration\Configuration;
use ApiGen\DI\Container\ContainerFactory;
use ApiGen\ModularConfiguration\Option\DestinationOption;
use ApiGen\ModularConfiguration\Option\SourceOption;
use Nette\DI\Container;
use PHPUnit\Framework\TestCase;

abstract class AbstractContainerAwareTestCase extends TestCase
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * @param mixed[] $data
     */
    public function __construct(?string $name = null, array $data = [], string $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->container = (new ContainerFactory)->create();

        $configuration = $this->container->getByType(Configuration::class);
        $configuration->resolveOptions([
            SourceOption::NAME => [__DIR__],
            DestinationOption::NAME => TEMP_DIR,
        ]);
    }
}
