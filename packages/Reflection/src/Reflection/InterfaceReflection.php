<?php declare(strict_types=1);

namespace ApiGen\Reflection\Reflection;

use ApiGen\Annotation\AnnotationList;
use ApiGen\Reflection\Contract\Reflection\ClassReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\InterfaceReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\MethodReflectionInterface;
use ApiGen\Reflection\Contract\TransformerCollectorInterface;
use phpDocumentor\Reflection\DocBlock;
use Roave\BetterReflection\Reflection\ReflectionClass;

final class InterfaceReflection implements InterfaceReflectionInterface
{
    /**
     * @var ReflectionClass
     */
    private $betterInterfaceReflection;

    /**
     * @var DocBlock
     */
    private $docBlock;

    public function __construct(ReflectionClass $betterInterfaceReflection, DocBlock $docBlock)
    {
        $this->betterInterfaceReflection = $betterInterfaceReflection;
        $this->docBlock = $docBlock;
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
        return $this->parserStorage->getDirectImplementersOfInterface($this);
    }

    /**
     * @return ClassReflectionInterface[]
     */
    public function getIndirectImplementers(): array
    {
        return $this->parserStorage->getIndirectImplementersOfInterface($this);
    }

    public function isDocumented(): bool
    {
        // TODO: Implement isDocumented() method.
    }

    public function getFileName(): string
    {
        // TODO: Implement getFileName() method.
    }

    /**
     * @return ClassReflectionInterface[]
     */
    public function getInterfaces(): array
    {
        // TODO: Implement getInterfaces() method.
    }

    /**
     * @return ClassReflectionInterface[]
     */
    public function getOwnInterfaces(): array
    {
        // TODO: Implement getOwnInterfaces() method.
    }

    /**
     * @return string[]
     */
    public function getOwnInterfaceNames(): array
    {
        // TODO: Implement getOwnInterfaceNames() method.
    }

    /**
     * @return MethodReflectionInterface[]
     */
    public function getMethods(): array
    {
        // TODO: Implement getMethods() method.
    }

    /**
     * @return MethodReflectionInterface[]
     */
    public function getOwnMethods(): array
    {
        // TODO: Implement getOwnMethods() method.
    }

    /**
     * @return MethodReflectionInterface[]
     */
    public function getInheritedMethods(): array
    {
        // TODO: Implement getInheritedMethods() method.
    }

    /**
     * @return MethodReflectionInterface[]
     */
    public function getUsedMethods(): array
    {
        // TODO: Implement getUsedMethods() method.
    }

    /**
     * @return MethodReflectionInterface[]
     */
    public function getTraitMethods(): array
    {
        // TODO: Implement getTraitMethods() method.
    }

    public function getMethod(string $name): MethodReflectionInterface
    {
        // TODO: Implement getMethod() method.
    }

    public function hasMethod(string $name): bool
    {
        // TODO: Implement hasMethod() method.
    }

    /**
     * @return ConstantReflectionInterface[]
     */
    public function getOwnConstants(): array
    {
        // TODO: Implement getOwnConstants() method.
    }

    /**
     * @return ConstantReflectionInterface[]
     */
    public function getInheritedConstants(): array
    {
        // TODO: Implement getInheritedConstants() method.
    }

    public function hasConstant(string $name): bool
    {
        // TODO: Implement hasConstant() method.
    }

    public function getConstant(string $name): ConstantReflectionInterface
    {
        // TODO: Implement getConstant() method.
    }

    public function getOwnConstant(string $name): ConstantReflectionInterface
    {
        // TODO: Implement getOwnConstant() method.
    }

    public function getTransformerCollector(): TransformerCollectorInterface
    {
        // TODO: Implement getTransformerCollector() method.
    }

    /**
     * @return ClassReflectionInterface[]
     */
    public function getParsedClasses(): array
    {
        // TODO: Implement getParsedClasses() method.
    }

    public function isSubclassOf(string $class): bool
    {
        // TODO: Implement isSubclassOf() method.
    }

    public function extendsInterface(string $interface): bool
    {
        return $this->betterInterfaceReflection->implementsInterface($interface);
    }
}
