<?php declare(strict_types=1);

namespace ApiGen\BetterReflection\SourceLocator;

use Roave\BetterReflection\SourceLocator\Type\AggregateSourceLocator;
use Roave\BetterReflection\SourceLocator\Type\AutoloadSourceLocator;
use Roave\BetterReflection\SourceLocator\Type\PhpInternalSourceLocator;
use Roave\BetterReflection\SourceLocator\Type\SingleFileSourceLocator;
use Roave\BetterReflection\SourceLocator\Type\SourceLocator;

final class FileSourceLocatorFactory
{
    /**
     * @param string[] $files
     */
    public function createFromFiles(array $files): SourceLocator
    {
        $locators = [
            new AutoloadSourceLocator,
            new PhpInternalSourceLocator,
        ];

        foreach ($files as $file) {
            $locators[] = new SingleFileSourceLocator($file);
        }

        return new AggregateSourceLocator($locators);
    }
}
