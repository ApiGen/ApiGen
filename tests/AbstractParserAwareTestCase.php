<?php declare(strict_types=1);

namespace ApiGen\Tests;

use ApiGen\Configuration\Configuration;
use ApiGen\DI\Container\ContainerFactory;
use ApiGen\ModularConfiguration\Option\DestinationOption;
use ApiGen\ModularConfiguration\Option\SourceOption;
use ApiGen\Reflection\Contract\ReflectionStorageInterface;
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
     * @var ReflectionStorageInterface
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
        $configuration = $this->container->getByType(Configuration::class);
        $configuration->resolveOptions([
            SourceOption::NAME => [__DIR__],
            DestinationOption::NAME => TEMP_DIR
        ]);

        $this->parser = $this->container->getByType(Parser::class);
        $this->reflectionStorage = $this->container->getByType(ReflectionStorageInterface::class);
    }
}
