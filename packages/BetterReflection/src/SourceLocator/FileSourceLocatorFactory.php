<?php declare(strict_types=1);

namespace ApiGen\BetterReflection\SourceLocator;

use Roave\BetterReflection\SourceLocator\Ast\Locator as AstLocator;
use Roave\BetterReflection\SourceLocator\Type\AggregateSourceLocator;
use Roave\BetterReflection\SourceLocator\Type\SingleFileSourceLocator;
use Roave\BetterReflection\SourceLocator\Type\SourceLocator;

final class FileSourceLocatorFactory
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
     * @param string[] $files
     */
    public function createFromFiles(array $files): SourceLocator
    {
        $locators = [];

        foreach ($files as $file) {
            $locators[] = new SingleFileSourceLocator($file, $this->astLocator);
        }

        return new AggregateSourceLocator($locators);
    }
}
