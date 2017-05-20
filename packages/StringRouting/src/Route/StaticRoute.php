<?php declare(strict_types=1);

namespace ApiGen\StringRouting\Route;

use ApiGen\Contracts\Configuration\ConfigurationInterface;
use ApiGen\ModularConfiguration\Option\DestinationOption;
use ApiGen\StringRouting\Contract\Route\RouteInterface;

final class StaticRoute implements RouteInterface
{
    /**
     * @var string
     */
    public const NAME = 'sourceCode';

    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    public function __construct(ConfigurationInterface $configuration)
    {
        $this->configuration = $configuration;
    }

    public function match(string $name): bool
    {
        return $name === self::NAME;
    }

    /**
     * @param string $reflection
     */
    public function constructUrl($filename): string
    {
        $filename = $this->configuration->getOption(DestinationOption::NAME) . '/' . $filename;
        if (is_file($filename)) {
            $filename .= '?' . md5_file($filename);
        }

        return $filename;
    }
}
