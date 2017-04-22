<?php declare(strict_types=1);

namespace ApiGen\ModularConfiguration;

use ApiGen\ModularConfiguration\Contract\ConfigurationDecoratorInterface;
use ApiGen\ModularConfiguration\Contract\Option\CommandOptionInterface;
use ApiGen\ModularConfiguration\Contract\Option\OptionInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;

final class ConfigurationDecorator implements ConfigurationDecoratorInterface
{
    /**
     * @var OptionInterface[]|CommandOptionInterface[]
     */
    private $options = [];

    public function addConfigurationOption(OptionInterface $option): void
    {
        $this->options[$option->getName()] = $option;
    }

    public function decorateCommand(Command $command): void
    {
        foreach ($this->options as $option) {
            if (! $this->isCommandOptionCandidate($option, $command)) {
                continue;
            }

            $command->addOption(
                $option->getName(),
                null,
                $this->getCommandMode($option),
                $option->getDescription(),
                $option->getDefaultValue()
            );
        }
    }

    private function getCommandMode(CommandOptionInterface $option): int
    {
        $commandMode = 0;
        if ($option->isValueRequired()) {
            $commandMode |= InputOption::VALUE_REQUIRED;
        }

        return $commandMode;
    }

    private function isCommandOptionCandidate(OptionInterface $option, Command $command): bool
    {
        if (! $option instanceof CommandOptionInterface) {
            return false;
        }

        return is_a($command, $option->getCommand());
    }
}
