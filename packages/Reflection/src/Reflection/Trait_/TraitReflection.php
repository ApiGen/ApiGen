<?php declare(strict_types=1);

namespace ApiGen\Reflection\Reflection\Trait_;

use ApiGen\Annotation\AnnotationList;
use ApiGen\Element\Tree\TraitUsersResolver;
use ApiGen\Reflection\Contract\Reflection\Class_\ClassMethodReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Class_\ClassReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Trait_\TraitMethodReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Trait_\TraitPropertyReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Trait_\TraitReflectionInterface;
use ApiGen\Reflection\Contract\Transformer\TransformerInterface;
use ApiGen\Reflection\Contract\TransformerCollectorAwareInterface;
use ApiGen\Reflection\Contract\TransformerCollectorInterface;
use phpDocumentor\Reflection\DocBlock;
use Roave\BetterReflection\Reflection\ReflectionClass;

final class TraitReflection implements TraitReflectionInterface, TransformerCollectorAwareInterface
{
    /**
     * @var ReflectionClass
     */
    private $betterTraitReflection;

    /**
     * @var DocBlock
     */
    private $docBlock;

    /**
     * @var TransformerCollectorInterface
     */
    private $transformerCollector;

    /**
     * @var TraitUsersResolver
     */
    private $traitUsersResolver;

    public function __construct(
        ReflectionClass $betterClassReflection,
        DocBlock $docBlock,
        TraitUsersResolver $traitUsersResolver
    ) {
        $this->betterTraitReflection = $betterClassReflection;
        $this->docBlock = $docBlock;
        $this->traitUsersResolver = $traitUsersResolver;
    }

    public function getName(): string
    {
        return $this->betterTraitReflection->getName();
    }

    /**
     * @return mixed[]
     */
    public function getAnnotation(string $name): array
    {
        return $this->docBlock->getTagsByName($name);
    }

    public function getDescription(): string
    {
        $description = $this->docBlock->getSummary()
            . AnnotationList::EMPTY_LINE
            . $this->docBlock->getDescription();

        return trim($description);
    }


    /**
     * @return ClassReflectionInterface[]
     */
    public function getUsers(): array
    {
        return $this->traitUsersResolver->getUsers($this);
    }

    public function getShortName(): string
    {
        return $this->betterTraitReflection->getShortName();
    }

    public function isDeprecated(): bool
    {
        // TODO: Implement isDeprecated() method.
    }

    public function getNamespaceName(): string
    {
        // TODO: Implement getNamespaceName() method.
    }

    public function getFileName(): string
    {
        return $this->betterTraitReflection->getFileName();
    }

    /**
     * @return TraitMethodReflectionInterface[]
     */
    public function getMethods(): array
    {
        return $this->transformerCollector->transformGroup(
            $this->betterTraitReflection->getMethods()
        );
    }

    /**
     * @return TraitMethodReflectionInterface[]
     */
    public function getOwnMethods(): array
    {
        // TODO: Implement getOwnMethods() method.
    }

    /**
     * @return ClassMethodReflectionInterface[]
     */
    public function getTraitMethods(): array
    {
        // TODO: Implement getTraitMethods() method.
    }

    public function getMethod(string $name): ClassMethodReflectionInterface
    {
        // TODO: Implement getMethod() method.
    }

    public function hasMethod(string $name): bool
    {
        // TODO: Implement hasMethod() method.
    }

    /**
     * @return TraitReflectionInterface[]
     */
    public function getTraits(): array
    {
        return $this->transformerCollector->transformGroup(
            $this->betterTraitReflection->getTraits()
        );
    }

    /**
     * @return TraitReflectionInterface[]
     */
    public function getOwnTraits(): array
    {
        // TODO: Implement getOwnTraits() method.
    }

    /**
     * @return string[]
     */
    public function getOwnTraitNames(): array
    {
        // TODO: Implement getOwnTraitNames() method.
    }

    /**
     * @return string[]
     */
    public function getTraitAliases(): array
    {
        // TODO: Implement getTraitAliases() method.
    }

    /**
     * @return TraitPropertyReflectionInterface[]
     */
    public function getProperties(): array
    {
        // TODO: Implement getProperties() method.
    }

    /**
     * @return TraitPropertyReflectionInterface[]
     */
    public function getOwnProperties(): array
    {
        return $this->transformerCollector->transformGroup(
            $this->betterTraitReflection->getImmediateProperties()
        );
    }

    /**
     * @return TraitPropertyReflectionInterface[]
     */
    public function getUsedProperties(): array
    {
        // TODO: Implement getUsedProperties() method.
    }

    public function getProperty(string $name): TraitPropertyReflectionInterface
    {
        // TODO: Implement getProperty() method.
    }

    public function hasProperty(string $name): bool
    {
        // TODO: Implement hasProperty() method.
    }

    public function usesTrait(string $name): bool
    {
        // TODO: Implement usesTrait() method.
    }

    /**
     * @return mixed[]
     */
    public function getAnnotations(): array
    {
        return $this->docBlock->getTags();
    }

    public function hasAnnotation(string $name): bool
    {
        return $this->docBlock->hasTag($name);
    }

    public function setTransformerCollector(TransformerCollectorInterface $transformerCollector): void
    {
        $this->transformerCollector = $transformerCollector;
    }
}
