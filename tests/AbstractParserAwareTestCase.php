<?php declare(strict_types=1);

namespace ApiGen\Tests;

use ApiGen\Configuration\Configuration;
use ApiGen\DependencyInjection\Container\ContainerFactory;
use ApiGen\ModularConfiguration\Option\DestinationOption;
use ApiGen\ModularConfiguration\Option\SourceOption;
use ApiGen\Reflection\ReflectionStorage;
use ApiGen\Reflection\Parser\Parser;
use Nette\DI\Container;
use PHPUnit\Framework\TestCase;

abstract class AbstractParserAwareTestCase extends TestCase
{
    /**
     * @var Container
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
            DestinationOption::NAME => TEMP_DIR
        ]);

        $this->parser = $this->container->get(Parser::class);
        $this->reflectionStorage = $this->container->get(ReflectionStorage::class);
    }
}
