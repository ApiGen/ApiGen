<?php declare(strict_types=1);

namespace ApiGen\Reflection\Reflection;

use ApiGen\Annotation\AnnotationList;
use ApiGen\Element\Tree\ImplementersResolver;
use ApiGen\Reflection\Contract\Reflection\ClassConstantReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\ClassReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\InterfaceReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\ClassMethodReflectionInterface;
use ApiGen\Reflection\Contract\TransformerCollectorAwareInterface;
use ApiGen\Reflection\Contract\TransformerCollectorInterface;
use phpDocumentor\Reflection\DocBlock;
use Roave\BetterReflection\Reflection\ReflectionClass;

final class InterfaceReflection implements InterfaceReflectionInterface, TransformerCollectorAwareInterface
{
    /**
     * @var ReflectionClass
     */
    private $betterInterfaceReflection;

    /**
     * @var DocBlock
     */
    private $docBlock;

    /**
     * @var TransformerCollectorInterface
     */
    private $transformerCollector;

    /**
     * @var ImplementersResolver
     */
    private $implementersResolver;

    public function __construct(
        ReflectionClass $betterInterfaceReflection,
        DocBlock $docBlock,
        ImplementersResolver $implementersResolver
    ) {
        $this->betterInterfaceReflection = $betterInterfaceReflection;
        $this->docBlock = $docBlock;
        $this->implementersResolver = $implementersResolver;
    }

    public function getStartLine(): int
    {
        return $this->betterInterfaceReflection->getStartLine();
    }

    public function getEndLine(): int
    {
        return $this->betterInterfaceReflection->getEndLine();
    }

    public function getName(): string
    {
        return $this->betterInterfaceReflection->getName();
    }

    public function getShortName(): string
    {
        return $this->betterInterfaceReflection->getShortName();
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
    public function getDirectImplementers(): array
    {
        return $this->implementersResolver->resolveDirectImplementersOfInterface($this->getName());
    }

    /**
     * @return ClassReflectionInterface[]
     */
    public function getIndirectImplementers(): array
    {
        return $this->implementersResolver->resolveIndirectImplementersOfInterface($this->getName());
    }

    public function isDocumented(): bool
    {
        // TODO: Implement isDocumented() method.
    }

    public function getFileName(): string
    {
        return $this->betterInterfaceReflection->getFileName();
    }

    /**
     * @return InterfaceReflectionInterface[]
     */
    public function getInterfaces(): array
    {
        return $this->transformerCollector->transformGroup(
            $this->betterInterfaceReflection->getInterfaces()
        );
    }

    /**
     * @return ClassMethodReflectionInterface[]
     */
    public function getMethods(): array
    {
        return $this->transformerCollector->transformGroup(
            $this->betterInterfaceReflection->getMethods()
        );
    }

    /**
     * @return ClassMethodReflectionInterface[]
     */
    public function getOwnMethods(): array
    {
        // TODO: Implement getOwnMethods() method.
    }

    /**
     * @return ClassMethodReflectionInterface[]
     */
    public function getInheritedMethods(): array
    {
        // TODO: Implement getInheritedMethods() method.
    }

    /**
     * @return ClassMethodReflectionInterface[]
     */
    public function getUsedMethods(): array
    {
        // TODO: Implement getUsedMethods() method.
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
     * @return ClassConstantReflectionInterface[]
     *
     * @todo replace with InterfaceConstantReflection!
     */
    public function getOwnConstants(): array
    {
        return $this->transformerCollector->transformGroup(
            $this->betterInterfaceReflection->getConstants()
        );
    }

    /**
     * @return ClassConstantReflectionInterface[]
     */
    public function getInheritedConstants(): array
    {
        // TODO: Implement getInheritedConstants() method.
    }

    public function hasConstant(string $name): bool
    {
        // TODO: Implement hasConstant() method.
    }

    public function getConstant(string $name): ClassConstantReflectionInterface
    {
        // TODO: Implement getConstant() method.
    }

    public function getOwnConstant(string $name): ClassConstantReflectionInterface
    {
        // TODO: Implement getOwnConstant() method.
    }

    /**
     * Actually "extends interface", but naming goes wrong on more places.
     * So decided to keep it here.
     */
    public function implementsInterface(string $interface): bool
    {
        return $this->betterInterfaceReflection->implementsInterface($interface);
    }

    public function setTransformerCollector(TransformerCollectorInterface $transformerCollector): void
    {
        $this->transformerCollector = $transformerCollector;
    }
}
