<?php declare(strict_types=1);

namespace ApiGen\BetterReflection\SourceLocator;

use Roave\BetterReflection\SourceLocator\Ast\Locator as AstLocator;
use Roave\BetterReflection\SourceLocator\Type\AggregateSourceLocator;
use Roave\BetterReflection\SourceLocator\Type\AutoloadSourceLocator;
use Roave\BetterReflection\SourceLocator\Type\ComposerSourceLocator;
use Roave\BetterReflection\SourceLocator\Type\DirectoriesSourceLocator;
use Roave\BetterReflection\SourceLocator\Type\PhpInternalSourceLocator;
use Roave\BetterReflection\SourceLocator\Type\SourceLocator;

final class DirectorySourceLocatorFactory
{
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
        $locators = [
            new DirectoriesSourceLocator($directories, $this->astLocator),
            new AutoloadSourceLocator($this->astLocator),
            new PhpInternalSourceLocator($this->astLocator),
        ];

        foreach ($directories as $directory) {
            $autoload = dirname($directory) . '/vendor/autoload.php';
            if (is_file($autoload)) {
                $locators[] = new ComposerSourceLocator(include $autoload);
            }
        }

        return new AggregateSourceLocator($locators);
    }
}
