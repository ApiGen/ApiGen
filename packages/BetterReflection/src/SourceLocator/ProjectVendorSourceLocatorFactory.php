<?php declare(strict_types=1);

namespace ApiGen\BetterReflection\SourceLocator;

use Roave\BetterReflection\SourceLocator\Ast\Locator as AstLocator;
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
     * Location, where an autoload is searched in.
     * @var string
     */
    private const STANDARD_AUTOLOAD_LOCATION = '/vendor/autoload.php';

    /**
     * @var AstLocator
     */
    private $astLocator;

    public function __construct(AstLocator $astLocator)
    {
        $this->astLocator = $astLocator;
    }

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
            $autoloadPath = $this->createAutoloadPath(dirname($directory));
            if (is_file($autoloadPath)) {
                return $this->createComposerSourceLocator($autoloadPath);
            }
        }

        return null;
    }

    private function createAutoloadPath(string $directory): string
    {
        return $directory . self::STANDARD_AUTOLOAD_LOCATION;
    }

    /**
     * $autoloadPath is expected to be composer autoload file path
     * It is also assumed that this php file will return \Composer\Autoload\ClassLoader after evaluation.
     */
    private function createComposerSourceLocator(string $autoloadPath): ComposerSourceLocator
    {
        return new ComposerSourceLocator(include $autoloadPath, $this->astLocator);
    }
}
