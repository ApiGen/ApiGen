<?php declare(strict_types=1);

namespace ApiGen\Reflection\DocBlock;

use ApiGen\Reflection\DocBlock\Tags\See;
use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\DocBlock\DescriptionFactory;
use phpDocumentor\Reflection\DocBlock\TagFactory;
use phpDocumentor\Reflection\DocBlockFactory as PhpDocumentorDocBlockFactory;
use phpDocumentor\Reflection\TypeResolver;
use Roave\BetterReflection\Reflection\ReflectionClass;
use Roave\BetterReflection\Reflection\ReflectionMethod;
use Roave\BetterReflection\Reflection\ReflectionProperty;

final class DocBlockFactory
{
    /**
     * @var PhpDocumentorDocBlockFactory
     */
    private $phpDocumentorDocBlockFactory;

    public function __construct(
        TagFactory $tagFactory,
        PhpDocumentorDocBlockFactory $docBlockFactory,
        DescriptionFactory $descriptionFactory,
        TypeResolver $typeResolver
    ) {
        $this->phpDocumentorDocBlockFactory = $docBlockFactory;

        // cannot move to services.yml, because it would cause circular dependency exception
        $tagFactory->registerTagHandler('see', See::class);
        $tagFactory->addService($descriptionFactory);
        $tagFactory->addService($typeResolver);
    }

    /**
     * @param ReflectionClass|ReflectionMethod|ReflectionProperty $classReflection
     */
    public function createFromBetterReflection($classReflection): DocBlock
    {
        return $this->phpDocumentorDocBlockFactory->create($classReflection->getDocComment() ?: ' ');
    }
}
