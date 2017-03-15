<?php declare(strict_types=1);

namespace ApiGen\Theme;

use ApiGen\Configuration\Exceptions\ConfigurationException;

class ThemeConfigPathResolver
{

    /**
     * @var string
     */
    private $rootDir;


    /**
     * @param string $rootDir
     */
    public function __construct($rootDir)
    {
        $this->rootDir = $rootDir;
    }


    /**
     * @param string $path
     * @return string
     */
    public function resolve($path)
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
