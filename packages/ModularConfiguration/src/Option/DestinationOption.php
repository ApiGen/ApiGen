<?php declare(strict_types=1);

namespace ApiGen\ModularConfiguration\Option;

use ApiGen\Console\Command\GenerateCommand;
use ApiGen\ModularConfiguration\Contract\Option\CommandOptionInterface;
use ApiGen\ModularConfiguration\Exception\ConfigurationException;
use ApiGen\Utils\FileSystem;

final class DestinationOption implements CommandOptionInterface
{
    /**
     * @var string
     */
    public const NAME = 'destination';

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
     */
    public function resolveValue($value): string
    {
        $this->validateValue($value);

        return $this->fileSystem->getAbsolutePath($value);
    }

    private function validateValue(?string $destination): void
    {
        if (! $destination) {
            throw new ConfigurationException(
                'Destination is not set. Use "--destination <directory>" to set it.'
            );
        }

        FileSystem::ensureDirectoryExists($destination);

        if (! is_writable($destination)) {
            throw new ConfigurationException(sprintf(
                'Destination "%s" is not writable.',
                $destination
            ));
        }
    }
}
