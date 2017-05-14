<?php declare(strict_types=1);

namespace ApiGen\Tests;

use ApiGen\Contracts\Configuration\ConfigurationInterface;
use ApiGen\DI\Container\ContainerFactory;
use ApiGen\ModularConfiguration\Option\DestinationOption;
use ApiGen\Reflection\Contract\ParserInterface;
use ApiGen\Reflection\Contract\ReflectionStorageInterface;
use PHPUnit\Framework\TestCase;
use Symplify\EasyCodingStandard\Configuration\ConfigurationNormalizer;
use Symplify\EasyCodingStandard\Configuration\ConfigurationOptions;

abstract class AbstractParserAwareTestCase extends TestCase
{
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

        $container = (new ContainerFactory)->create();

        /** @var ConfigurationInterface $configuration */
        $configuration = $container->getByType(ConfigurationInterface::class);
        $configuration->resolveOptions([
            DestinationOption::NAME => TEMP_DIR
        ]);

        $this->parser = $container->getByType(ParserInterface::class);
        $this->reflectionStorage = $container->getByType(ReflectionStorageInterface::class);
    }
}
