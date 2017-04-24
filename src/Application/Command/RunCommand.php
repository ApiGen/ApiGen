<?php declare(strict_types=1);

namespace ApiGen\Application\Command;

use ApiGen\Configuration\ConfigurationOptions;
use ApiGen\ModularConfiguration\Option\ConfigurationFileOption;
use ApiGen\ModularConfiguration\Option\DestinationOption;
use Symfony\Component\Console\Input\InputInterface;

final class RunCommand
{
    /**
     * @var string[]
     */
    private $source = [];

    /**
     * @var string
     */
    private $destination;

    /**
     * @var string
     */
    private $configPath;

    /**
     * @param string[] $source
     * @param string $destination
     * @param string $configPath
     */
    private function __construct(array $source, string $destination, string $configPath)
    {
        $this->source = $source;
        $this->destination = $destination;
        $this->configPath = $configPath;
    }

    public static function createFromInput(InputInterface $input): self
    {
        return new self(
            $input->getArgument(ConfigurationOptions::SOURCE),
            $input->getOption(DestinationOption::NAME),
            $input->getOption(ConfigurationFileOption::NAME)
        );
    }

    /**
     * @return string[]
     */
    public function getSource(): array
    {
        return $this->source;
    }

    public function getDestination(): string
    {
        return $this->destination;
    }

    public function getConfigPath(): string
    {
        return $this->configPath;
    }
}
