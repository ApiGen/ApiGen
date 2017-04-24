<?php declare(strict_types=1);

namespace ApiGen\ModularConfiguration;

use ApiGen\ModularConfiguration\Contract\ConfigurationDecoratorInterface;
use ApiGen\ModularConfiguration\Contract\Option\CommandArgumentInterface;
use ApiGen\ModularConfiguration\Contract\Option\CommandBoundInterface;
use ApiGen\ModularConfiguration\Contract\Option\CommandOptionInterface;
use ApiGen\ModularConfiguration\Contract\Option\OptionInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
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
            if (! $this->isCommandCandidate($option, $command)) {
                continue;
            }

            if ($option instanceof CommandArgumentInterface) {
                $this->addCommandArgument($command, $option);
            }

            if ($option instanceof CommandOptionInterface) {
                $this->addCommandOption($command, $option);
            }
        }
    }

    private function isCommandCandidate(OptionInterface $option, Command $command): bool
    {
        if (! $option instanceof CommandBoundInterface) {
            return false;
        }

        return is_a($command, $option->getCommand());
    }

    private function addCommandArgument(Command $command, CommandArgumentInterface $argument): void
    {
        $command->addArgument(
            $argument->getName(),
            $this->getCommandArgumentMode($argument),
            $argument->getDescription()
        );
    }

    private function getCommandArgumentMode(CommandArgumentInterface $argument): int
    {
        $mode = 0;
        if ($argument->isValueRequired()) {
            $mode |= InputArgument::REQUIRED;
        }

        if ($argument->isArray()) {
            $mode |= InputArgument::IS_ARRAY;
        }

        return $mode;
    }

    private function addCommandOption(Command $command, CommandOptionInterface $option): void
    {
        $command->addOption(
            $option->getName(),
            null,
            $this->getCommandOptionMode($option),
            $option->getDescription(),
            $option->getDefaultValue()
        );
    }

    private function getCommandOptionMode(CommandOptionInterface $option): int
    {
        $mode = 0;
        if ($option->isValueRequired()) {
            $mode |= InputOption::VALUE_REQUIRED;
        }

        return $mode;
    }
}
