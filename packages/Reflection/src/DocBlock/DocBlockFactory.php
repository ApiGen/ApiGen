<?php declare(strict_types=1);

namespace ApiGen\Reflection\DocBlock;

use ApiGen\Reflection\DocBlock\Tags\See;
use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\DocBlock\DescriptionFactory;
use phpDocumentor\Reflection\DocBlock\TagFactory;
use phpDocumentor\Reflection\DocBlockFactory as PhpDocumentorDocBlockFactory;
use phpDocumentor\Reflection\TypeResolver;
use phpDocumentor\Reflection\Types\Context;
use Roave\BetterReflection\Reflection\ReflectionClass;
use Roave\BetterReflection\Reflection\ReflectionFunction;
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
        $context = $this->createDocBlockContext($classReflection);
        return $this->phpDocumentorDocBlockFactory->create($classReflection->getDocComment() ?: ' ', $context);
    }

    /**
     * Creates a context for proper type resolving.
     *
     * @param ReflectionClass|ReflectionMethod|ReflectionProperty|ReflectionFunction $reflection
     */
    private function createDocBlockContext($reflection): ?Context
    {
        if (! $reflection instanceof ReflectionClass && ! $reflection instanceof ReflectionFunction) {
            $reflection = $reflection->getDeclaringClass();
        }

        return (new \phpDocumentor\Reflection\Types\ContextFactory())->createForNamespace(
            $reflection->getNamespaceName(),
            $reflection->getLocatedSource()->getSource()
        );
    }
}
