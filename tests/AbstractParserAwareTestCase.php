<?php declare(strict_types=1);

namespace ApiGen\Tests;

use ApiGen\Contract\Configuration\ConfigurationInterface;
use ApiGen\DI\Container\ContainerFactory;
use ApiGen\ModularConfiguration\Option\DestinationOption;
use ApiGen\ModularConfiguration\Option\SourceOption;
use ApiGen\Reflection\Contract\ParserInterface;
use ApiGen\Reflection\Contract\ReflectionStorageInterface;
use Nette\DI\Container;
use PHPUnit\Framework\TestCase;
use Symplify\EasyCodingStandard\Configuration\ConfigurationNormalizer;
use Symplify\EasyCodingStandard\Configuration\ConfigurationOptions;

abstract class AbstractParserAwareTestCase extends TestCase
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * @var ParserInterface
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

        /** @var ConfigurationInterface $configuration */
        $configuration = $this->container->getByType(ConfigurationInterface::class);
        $configuration->resolveOptions([
            SourceOption::NAME => [__DIR__],
            DestinationOption::NAME => TEMP_DIR
        ]);

        $this->parser = $this->container->getByType(ParserInterface::class);
        $this->reflectionStorage = $this->container->getByType(ReflectionStorageInterface::class);
    }
}
