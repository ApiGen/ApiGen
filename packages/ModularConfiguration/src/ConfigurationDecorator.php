<?php declare(strict_types=1);

namespace ApiGen\ModularConfiguration;

use ApiGen\ModularConfiguration\Contract\CommandOptionInterface;
use ApiGen\ModularConfiguration\Contract\ConfigurationDecoratorInterface;
use ApiGen\ModularConfiguration\Contract\OptionInterface;
use Symfony\Component\Console\Command\Command;

final class ConfigurationDecorator implements ConfigurationDecoratorInterface
{
    /**
     * @var OptionInterface[]
     */
    private $configurationOptions = [];

    public function addConfigurationOption(OptionInterface $configurationOption): void
    {
        $this->configurationOptions[$configurationOption->getName()] = $configurationOption;
    }


    public function decorateCommand(Command $command): void
    {
        foreach ($this->configurationOptions as $configurationOption) {
            if (! $configurationOption instanceof CommandOptionInterface) {
                continue;
            }

            dump($this->configurationOptions);
            die;
        }
    }
}
