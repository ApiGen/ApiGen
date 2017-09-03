<?php declare(strict_types=1);

namespace ApiGen\Tests;

use ApiGen\Configuration\Configuration;
use ApiGen\DependencyInjection\Container\ContainerFactory;
use ApiGen\ModularConfiguration\Option\DestinationOption;
use ApiGen\ModularConfiguration\Option\SourceOption;
use ApiGen\Reflection\Parser\Parser;
use ApiGen\Reflection\ReflectionStorage;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

abstract class AbstractParserAwareTestCase extends TestCase
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var Parser
     */
    protected $parser;

    /**
     * @var ReflectionStorage
     */
    protected $reflectionStorage;

    /**
     * @var Configuration
     */
    protected $configuration;

    /**
     * @param mixed[] $data
     */
    public function __construct(?string $name = null, array $data = [], string $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->container = (new ContainerFactory)->create();

        $this->configuration = $this->container->get(Configuration::class);
        $this->configuration->resolveOptions([
            SourceOption::NAME => [__DIR__],
            DestinationOption::NAME => TEMP_DIR,
        ]);

        $this->parser = $this->container->get(Parser::class);
        $this->reflectionStorage = $this->container->get(ReflectionStorage::class);
    }

    /**
     * @param string[] $source
     */
    protected function resolveConfigurationBySource(array $source): void
    {
        $this->configuration->resolveOptions([
            SourceOption::NAME => $source,
            DestinationOption::NAME => TEMP_DIR,
        ]);
    }
}
