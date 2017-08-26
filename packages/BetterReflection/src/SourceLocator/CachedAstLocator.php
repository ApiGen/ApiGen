<?php declare(strict_types=1);

namespace ApiGen\BetterReflection\SourceLocator;

use PhpParser\Parser;
use Roave\BetterReflection\Identifier\Identifier;
use Roave\BetterReflection\Identifier\IdentifierType;
use Roave\BetterReflection\Reflection\Reflection;
use Roave\BetterReflection\Reflector\Reflector;
use Roave\BetterReflection\SourceLocator\Ast\FindReflectionsInTree;
use Roave\BetterReflection\SourceLocator\Ast\Locator;
use Roave\BetterReflection\SourceLocator\Located\LocatedSource;

/**
 * Based on https://github.com/Roave/BetterReflection/pull/312
 */
final class CachedAstLocator implements Locator
{
    /**
     * @var Parser
     */
    private $parser;

    /**
     * @var FindReflectionsInTree
     */
    private $findReflectionsInTree;

    public function __construct(FindReflectionsInTree $findReflectionsInTree, Parser $parser)
    {
        $this->findReflectionsInTree = $findReflectionsInTree;
        $this->parser = $parser;
    }

    public function findReflection(
        Reflector $reflector,
        LocatedSource $locatedSource,
        Identifier $identifier
    ): Reflection {
        // ...
    }

    /**
     * @return mixed[]
     */
    public function findReflectionsOfType(
        Reflector $reflector,
        LocatedSource $locatedSource,
        IdentifierType $identifierType
    ): array {
        // ...
    }
}
