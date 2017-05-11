<?php declare(strict_types=1);

namespace ApiGen\Reflection\Reflection\Class_;

use ApiGen\Annotation\AnnotationList;
use ApiGen\Element\Tree\ClassTraitElementResolver;
use ApiGen\Element\Tree\SubClassesResolver;
use ApiGen\Reflection\Contract\Reflection\Class_\ClassReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Class_\ClassMethodReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Class_\ClassPropertyReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Class_\ClassConstantReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Interface_\InterfaceReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Trait_\TraitMethodReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Trait_\TraitReflectionInterface;
use ApiGen\Reflection\Contract\TransformerCollectorAwareInterface;
use ApiGen\Reflection\Contract\TransformerCollectorInterface;
use ApiGen\Element\Tree\ParentClassElementsResolver;
use phpDocumentor\Reflection\DocBlock;
use Roave\BetterReflection\Reflection\ReflectionClass;

final class ClassReflection implements ClassReflectionInterface, TransformerCollectorAwareInterface
{
    /**
     * @var ReflectionClass
     */
    private $betterClassReflection;

    /**
     * @var DocBlock
     */
    private $docBlock;

    /**
     * @var TransformerCollectorInterface
     */
    private $transformerCollector;

    /**
     * @var ParentClassElementsResolver
     */
    private $parentClassElementsResolver;

    /**
     * @var ClassTraitElementResolver
     */
    private $classTraitElementResolver;

    /**
     * @var SubClassesResolver
     */
    private $subClassesResolver;

    public function __construct(
        ReflectionClass $betterClassReflection,
        DocBlock $docBlock,
        ParentClassElementsResolver $parentClassElementsResolver,
        ClassTraitElementResolver $classTraitElementResolver,
        SubClassesResolver $subClassesResolver
    ) {
        $this->betterClassReflection = $betterClassReflection;
        $this->docBlock = $docBlock;
        $this->parentClassElementsResolver = $parentClassElementsResolver;
        $this->classTraitElementResolver = $classTraitElementResolver;
        $this->subClassesResolver = $subClassesResolver;
    }

    public function getName(): string
    {
        return $this->betterClassReflection->getName();
    }

    public function getShortName(): string
    {
        return $this->betterClassReflection->getShortName();
    }

    public function getStartLine(): int
    {
        return $this->betterClassReflection->getStartLine();
    }

    public function getEndLine(): int
    {
        return $this->betterClassReflection->getEndLine();
    }

    public function getNamespaceName(): string
    {
        return $this->betterClassReflection->getNamespaceName();
    }

//        public function getNamespaceName(): string
//    {
//        static $namespaces = [];
//
//        $namespaceName = $this->reflection->getNamespaceName();
//
//        if (! $namespaceName) {
//            return $namespaceName;
//        }
//
//        $lowerNamespaceName = strtolower($namespaceName);
//        if (! isset($namespaces[$lowerNamespaceName])) {
//            $namespaces[$lowerNamespaceName] = $namespaceName;
//        }
//
//        return $namespaces[$lowerNamespaceName];
//    }

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

    public function getParentClass(): ?ClassReflectionInterface
    {
        if ($this->betterClassReflection->getParentClass()) {
            return $this->transformerCollector->transformSingle(
                $this->betterClassReflection->getParentClass()
            );
        }

        return null;
    }

    public function getParentClassName(): string
    {
        if ($this->betterClassReflection->getParentClass()) {
            return $this->betterClassReflection->getParentClass()
                ->getName();
        }

        return '';
    }

    /**
     * @return ClassReflectionInterface[]
     */
    public function getParentClasses(): array
    {
        $parentClasses = [];

        $currentClass = $this->getParentClass();
        while ($currentClass) {
            $parentClasses[$currentClass->getName()] = $currentClass;
            $currentClass = $currentClass->getParentClass();
        }

        return $parentClasses;
    }

    /**
     * @return ClassReflectionInterface[]
     */
    public function getSubClasses(): array
    {
        return $this->subClassesResolver->getSubClasses($this);
    }

    public function implementsInterface(string $interface): bool
    {
        return $this->betterClassReflection->implementsInterface($interface);
    }

    /**
     * @return ClassReflectionInterface[]
     */
    public function getOwnInterfaces(): array
    {
        return array_map(function (IReflectionClass $class) {
            return $this->getParsedClasses()[$class->getName()];
        }, $this->betterClassReflection->getOwnInterfaces());
    }

    /**
     * @return string[]
     */
    public function getOwnInterfaceNames(): array
    {
        return array_keys($this->betterClassReflection->getImmediateInterfaces());
    }

    public function getMethod(string $name): ClassMethodReflectionInterface
    {
        if ($this->hasMethod($name)) {
            return $this->getMethods()[$name];
        }

        throw new \InvalidArgumentException(sprintf(
            'Method "%s" does not exist in "%s" class.',
            $name,
            $this->getName()
        ));
    }

    /**
     * @return ClassMethodReflectionInterface[]
     */
    public function getInheritedMethods(): array
    {
        return $this->parentClassElementsResolver->getInheritedMethods($this);
    }

    /**
     * @return ClassMethodReflectionInterface[]
     */
    public function getUsedMethods(): array
    {
        $usedMethods = $this->classTraitElementResolver->getUsedMethods($this);

        return $this->sortUsedMethods($usedMethods);
    }


    public function hasMethod(string $name): bool
    {
        return isset($this->getMethods()[$name]);
    }

    /**
     * @return ClassConstantReflectionInterface[]
     */
    public function getConstants(): array
    {
        return $this->transformerCollector->transformGroup(
            $this->betterClassReflection->getConstants()
        );
    }

    /**
     * @return ClassConstantReflectionInterface[]
     */
    public function getOwnConstants(): array
    {
        $ownConstants = [];
        foreach ($this->getConstants() as $constantName => $constant) {
            if ($constant->getDeclaringClassName() === $this->getName()) {
                $ownConstants[$constantName] = $constant;
            }
        }
        return $ownConstants;
    }

    /**
     * @return ClassConstantReflectionInterface[]
     */
    public function getInheritedConstants(): array
    {
        return $this->parentClassElementsResolver->getInheritedConstants($this);
    }

    public function hasConstant(string $name): bool
    {
        return isset($this->getConstants()[$name]);
    }

    public function getConstant(string $name): ClassConstantReflectionInterface
    {
        if (isset($this->getConstants()[$name])) {
            return $this->getConstants()[$name];
        }

        throw new InvalidArgumentException(sprintf(
            'Constant %s does not exist in class %s',
            $name,
            $this->betterClassReflection->getName()
        ));
    }

    public function getOwnConstant(string $name): ClassConstantReflectionInterface
    {
        // TODO: Implement getOwnConstant() method.
    }

    /**
     * @return ClassReflectionInterface[]
     */
    public function getTraits(): array
    {
        return $this->transformerCollector->transformGroup(
            $this->betterClassReflection->getTraits()
        );
    }

    /**
     * @return string[]
     */
    public function getTraitNames(): array
    {
        return $this->betterClassReflection->getTraitNames();
    }

    /**
     * @return string[]
     */
    public function getTraitAliases(): array
    {
        return $this->betterClassReflection->getTraitAliases();
    }

    /**
     * @return ClassPropertyReflectionInterface[]
     */
    public function getProperties(): array
    {
        return $this->transformerCollector->transformGroup(
            $this->betterClassReflection->getProperties()
        );
    }

    /**
     * @return ClassPropertyReflectionInterface[]
     */
    public function getOwnProperties(): array
    {
        return $this->transformerCollector->transformGroup(
            $this->betterClassReflection->getImmediateProperties()
        );
    }

    /**
     * @return ClassPropertyReflectionInterface[][]
     */
    public function getInheritedProperties(): array
    {
        return $this->parentClassElementsResolver->getInheritedProperties($this);
    }

    /**
     * @return ClassPropertyReflectionInterface[][]
     */
    public function getUsedProperties(): array
    {
        return $this->classTraitElementResolver->getUsedProperties($this);
    }

    public function getProperty(string $name): ClassPropertyReflectionInterface
    {
        if (! $this->hasProperty($name)) {
            throw new \InvalidArgumentException(sprintf(
                'Property %s does not exist in class %s',
                $name,
                $this->getName()
            ));
        }

        return $this->getProperties()[$name];
    }

    public function hasProperty(string $name): bool
    {
        return isset($this->getProperties()[$name]);
    }

    public function usesTrait(string $trait): bool
    {
        return isset($this->getTraits()[$trait]);
    }

    public function isAbstract(): bool
    {
        return $this->betterClassReflection->isAbstract();
    }

    public function isFinal(): bool
    {
        return $this->betterClassReflection->isFinal();
    }

    public function isSubclassOf(string $class): bool
    {
        return $this->betterClassReflection->isSubclassOf($class);
    }


    public function isDeprecated(): bool
    {
        return $this->hasAnnotation(AnnotationList::DEPRECATED);
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

    public function getFileName(): string
    {
        return $this->betterClassReflection->getFileName();
    }



    /**
     * @param mixed[] $usedMethods
     * @return mixed[]
     */
    private function sortUsedMethods(array $usedMethods): array
    {
        array_walk($usedMethods, function (&$methods) {
            ksort($methods);
            array_walk($methods, function (&$aliasedMethods) {
                if (! isset($aliasedMethods['aliases'])) {
                    $aliasedMethods['aliases'] = [];
                }

                ksort($aliasedMethods['aliases']);
            });
        });

        return $usedMethods;
    }

    /**
     * @return ClassMethodReflectionInterface[]|TraitMethodReflectionInterface[]
     */
    public function getMethods(): array
    {
        $allMethods = [];
        $allMethods += $this->getOwnMethods();

        foreach ($this->getTraits() as $traitReflection) {
            // @todo: check override from the left
            // keep already existing
            $allMethods += $traitReflection->getMethods();
        }

        if ($this->getParentClass()) {
            // @todo: check override from the left
            // keep already existing
            $allMethods += $this->getParentClass()->getMethods();
        }

        return $allMethods;
    }

    /**
     * @return ClassMethodReflectionInterface[]
     */
    public function getOwnMethods(): array
    {
        return $this->transformerCollector->transformGroup(
            $this->betterClassReflection->getImmediateMethods()
        );
    }

    /**
     * @return TraitMethodReflectionInterface[]
     */
    public function getTraitMethods(): array
    {
        $traitMethods = [];
        foreach ($this->getTraits() as $traitReflection) {
            $traitMethods = array_merge($traitMethods, $traitReflection->getMethods());
        }
        return $traitMethods;
    }

    /**
     * @return InterfaceReflectionInterface[]
     */
    public function getInterfaces(): array
    {
        return $this->transformerCollector->transformGroup(
            $this->betterClassReflection->getInterfaces()
        );
    }

    public function setTransformerCollector(TransformerCollectorInterface $transformerCollector): void
    {
        $this->transformerCollector = $transformerCollector;
    }
}
