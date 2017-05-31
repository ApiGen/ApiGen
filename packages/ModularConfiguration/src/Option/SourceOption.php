<?php declare(strict_types=1);

namespace ApiGen\ModularConfiguration\Option;

use ApiGen\Console\Command\GenerateCommand;
use ApiGen\ModularConfiguration\Contract\Option\CommandArgumentInterface;
use ApiGen\ModularConfiguration\Exception\ConfigurationException;
use ApiGen\Utils\FileSystem;

final class SourceOption implements CommandArgumentInterface
{
    /**
     * @var string
     */
    public const NAME = 'source';

    /**
     * @var FileSystem
     */
    private $fileSystem;

    public function __construct(FileSystem $fileSystem)
    {
        $this->fileSystem = $fileSystem;
    }

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
     * @param string[] $value
     * @return string[]
     */
    public function resolveValue($value): array
    {
        $this->ensureSourcesExist($value);

        foreach ($value as $key => $source) {
            $value[$key] = $this->fileSystem->getAbsolutePath($source);
        }

        return $value;
    }

    /**
     * @param string[] $sources
     */
    private function ensureSourcesExist(array $sources): void
    {
        foreach ($sources as $source) {
            if (! file_exists($source)) {
                throw new ConfigurationException(sprintf(
                    'Source "%s" does not exist',
                    $source
                ));
            }
        }
    }
}
