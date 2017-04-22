<?php declare(strict_types=1);

namespace ApiGen\ModularConfiguration\Option;

use ApiGen\Console\Command\GenerateCommand;
use ApiGen\ModularConfiguration\Contract\Option\CommandOptionInterface;

final class DestinationOption implements CommandOptionInterface
{
    /**
     * @var string
     */
    public const NAME = 'destination';

    public function getCommand(): string
    {
        return GenerateCommand::class;
    }

    public function getName(): string
    {
        return self::NAME;
    }

    public function getDescription(): string
    {
        return 'Target dir for generated documentation.';
    }

    public function isValueRequired(): bool
    {
        return true;
    }

    /**
     * @return mixed|null
     */
    public function getDefaultValue()
    {
        return null;
    }

    /**
     * @param mixed $value
     * @return mixed
     */
    public function resolveValue($value)
    {
        // TODO: Implement resolveValue() method.
    }
}
