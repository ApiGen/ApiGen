<?php declare(strict_types=1);

namespace ApiGen\ModularConfiguration\Option;

use ApiGen\Console\Command\GenerateCommand;
use ApiGen\ModularConfiguration\Contract\Option\CommandArgumentInterface;

final class SourceOption implements CommandArgumentInterface
{
    /**
     * @var string
     */
    public const NAME = 'source';

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
        return 'Dirs or files documentation is generated for.';
    }

    public function isValueRequired(): bool
    {
        return true;
    }

    public function isArray(): bool
    {
        return true;
    }

    /**
     * @param mixed $value
     * @return string[]
     */
    public function resolveValue($value): array
    {

    }
}
