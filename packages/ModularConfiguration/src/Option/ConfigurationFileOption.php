<?php declare(strict_types=1);

namespace ApiGen\ModularConfiguration\Option;

use ApiGen\Console\Command\GenerateCommand;
use ApiGen\ModularConfiguration\Contract\Option\CommandOptionInterface;

final class ConfigurationFileOption implements CommandOptionInterface
{
    /**
     * @var string
     */
    public const NAME = 'config';

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
        return 'Path to apigen.neon config file.';
    }

    public function isValueRequired(): bool
    {
        return true;
    }

    public function getDefaultValue(): string
    {
        return getcwd() . DIRECTORY_SEPARATOR . 'apigen.neon';
    }

    /**
     * @param mixed $value
     * @return mixed
     */
    public function resolveValue($value): string
    {
        return $value ?: '';
    }
}
