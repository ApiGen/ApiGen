<?php declare(strict_types=1);

namespace ApiGen\BetterReflection\SourceLocator;

use Roave\BetterReflection\SourceLocator\Type\AggregateSourceLocator;
use Roave\BetterReflection\SourceLocator\Type\ComposerSourceLocator;
use Roave\BetterReflection\SourceLocator\Type\SourceLocator;

/**
 * This service will try to find project's autoload, to be able to parse its classes.
 * It might be the same as autoload of ApiGen, but it might differ.
 * E.g. in case you create ApiGen with "composer create-project".
 */
final class ProjectVendorSourceLocatorFactory
{
    /**
     * @param string[] $directories
     */
    public function createFromDirectories(array $directories): SourceLocator
    {
        $composerSourceLocator = $this->tryToFindProjectAutoload($directories);
        if ($composerSourceLocator !== null) {
            return $composerSourceLocator;
        }

        return new AggregateSourceLocator;
    }

    /**
     * @param string[] $directories
     */
    private function tryToFindProjectAutoload(array $directories): ?ComposerSourceLocator
    {
        foreach ($directories as $directory) {
            $autoload = dirname($directory) . '/vendor/autoload.php';
            if (is_file($autoload)) {
                return new ComposerSourceLocator(include $autoload);
            }
        }

        return null;
    }
}
