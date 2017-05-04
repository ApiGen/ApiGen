<?php declare(strict_types=1);

namespace ApiGen\ModularConfiguration\Option;

use ApiGen\ModularConfiguration\Exception\ConfigurationException;
use ApiGen\ModularConfiguration\Contract\Option\OptionInterface;
use ApiGen\Utils\FileSystem;

final class ThemeDirectoryOption implements OptionInterface
{
    /**
     * @var string
     */
    public const NAME = 'themeDirectory';

    /**
     * @var FileSystem
     */
    private $fileSystem;

    public function __construct(FileSystem $fileSystem)
    {
        $this->fileSystem = $fileSystem;
    }

    public function getName(): string
    {
        return self::NAME;
    }

    /**
     * @param string $value
     */
    public function resolveValue($value): string
    {
        if ($value) {
            $value = $this->fileSystem->getAbsolutePath($value);
            $this->validateDirectory($value);

            return $value;
        }

        return getcwd() . '/packages/ThemeDefault/src';
    }

    private function validateDirectory(string $value): void
    {
        if (! is_dir($value)) {
            throw new ConfigurationException(sprintf(
                'Theme directory "%s" was not found.',
                $value
            ));
        }
    }
}
