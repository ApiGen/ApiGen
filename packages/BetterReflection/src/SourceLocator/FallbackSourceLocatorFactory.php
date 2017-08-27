<?php declare(strict_types=1);

namespace ApiGen\BetterReflection\SourceLocator;

use Roave\BetterReflection\SourceLocator\Ast\Locator as AstLocator;
use Roave\BetterReflection\SourceLocator\Type\AggregateSourceLocator;
use Roave\BetterReflection\SourceLocator\Type\AutoloadSourceLocator;
use Roave\BetterReflection\SourceLocator\Type\PhpInternalSourceLocator;
use Roave\BetterReflection\SourceLocator\Type\SourceLocator;

final class FallbackSourceLocatorFactory
{
    /**
     * @var AstLocator
     */
    private $astLocator;

    /**
     * @var ProjectVendorSourceLocatorFactory
     */
    private $projectVendorSourceLocatorFactory;

    public function __construct(
        AstLocator $astLocator,
        ProjectVendorSourceLocatorFactory $projectVendorSourceLocatorFactory
    ) {
        $this->astLocator = $astLocator;
        $this->projectVendorSourceLocatorFactory = $projectVendorSourceLocatorFactory;
    }

    /**
     * @param string[] $directories
     */
    public function createFromDirecotires(array $directories): SourceLocator
    {
        return new AggregateSourceLocator([
            new AutoloadSourceLocator($this->astLocator),
            new PhpInternalSourceLocator($this->astLocator),
            $this->projectVendorSourceLocatorFactory->createFromDirectories($directories),
        ]);
    }
}
