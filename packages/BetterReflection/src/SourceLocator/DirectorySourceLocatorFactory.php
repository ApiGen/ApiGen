<?php declare(strict_types=1);

namespace ApiGen\BetterReflection\SourceLocator;

use Roave\BetterReflection\SourceLocator\Ast\Locator as AstLocator;
use Roave\BetterReflection\SourceLocator\Type\DirectoriesSourceLocator;
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
        return new DirectoriesSourceLocator($directories, $this->astLocator);
    }
}
