<?php declare(strict_types=1);

namespace ApiGen\Theme;

use ApiGen\Configuration\Exceptions\ConfigurationException;

class ThemeConfigPathResolver
{

    /**
     * @var string
     */
    private $rootDir;


    public function __construct(string $rootDir)
    {
        $this->rootDir = $rootDir;
    }


    public function resolve(string $path): string
    {
        $allowedPaths = [
            $this->rootDir,
            $this->rootDir . '/../../..'
        ];

        foreach ($allowedPaths as $allowedPath) {
            $absolutePath = $allowedPath . '/' . ltrim($path, DIRECTORY_SEPARATOR);
            if (file_exists($absolutePath)) {
                return $absolutePath;
            }
        }

        throw new ConfigurationException(sprintf('Config "%s" was not found.', $path));
    }
}
