<?php declare(strict_types=1);

namespace ApiGen\Reflection\Reflection;

use ApiGen\Annotation\AnnotationList;
use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ConstantReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\MethodReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\PropertyReflectionInterface;
use ApiGen\Reflection\Contract\TransformerCollectorInterface;
use phpDocumentor\Reflection\DocBlock;
use Roave\BetterReflection\Reflection\ReflectionClass;

final class ClassReflection implements ClassReflectionInterface
{
    /**
     * @var ReflectionClass
     */
    private $reflection;

    /**
     * @var DocBlock
     */
    private $docBlock;

    public function __construct(
        ReflectionClass $betterClassReflection,
        DocBlock $docBlock
    ) {
        $this->reflection = $betterClassReflection;
        $this->docBlock = $docBlock;
    }

    public function getName(): string
    {
        return $this->reflection->getName();
    }

    public function getShortName(): string
    {
        return $this->reflection->getShortName();
    }

    public function getStartLine(): int
    {
        return $this->reflection->getStartLine();
    }

    public function getEndLine(): int
    {
        return $this->reflection->getEndLine();
    }

    public function getNamespaceName(): string
    {
        return $this->reflection->getNamespaceName();

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

// drop those "PHP" elements
//        public function getPseudoNamespaceName(): string
//    {
//        return $this->isInternal() ? 'PHP' : $this->getNamespaceName() ?: 'None';
//    }
    }

    public function getPseudoNamespaceName(): string
    {
        if ($this->reflection->isInternal()) {
            return 'PHP';
        }

        if ($this->reflection->getNamespaceName()) {
            return $this->reflection->getNamespaceName();
        }

        return 'None';
    }

    public function getPrettyName(): string
    {
        return $this->reflection->getName() . '()';
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

    public function isDocumented(): bool
    {
        if ($this->reflection->isInternal()) {
            return false;
        }

        if ($this->hasAnnotation('internal')) {
            return false;
        }

        return true;
    }

    public function getParentClass(): ?ClassReflectionInterface
    {
        $parentClassName = $this->reflection->getParentClassName();

        if ($parentClassName) {
            return $this->getParsedClasses()[$parentClassName];
        }

        return null;
    }

    public function getParentClassName(): string
    {
        if ($this->reflection->getParentClass()) {
            return $this->reflection->getParentClass()
                ->getShortName();
        }

        return '';
    }

    /**
     * @return ClassReflectionInterface[]
     */
    public function getParentClasses(): array
    {
        if ($this->parentClasses === null) {
            $this->parentClasses = array_map(function (IReflectionClass $class) {
                return $this->getParsedClasses()[$class->getName()];
            }, $this->reflection->getParentClasses());
        }

        return $this->parentClasses;
    }


    /**
     * @return ClassReflectionInterface[]
     */
    public function getDirectSubClasses(): array
    {
        $subClasses = [];
        foreach ($this->getParsedClasses() as $class) {
            if ($class->isDocumented() && $this->getName() === $class->getParentClassName()) {
                $subClasses[] = $class;
            }
        }

        uksort($subClasses, 'strcasecmp');
        return $subClasses;
    }

    /**
     * @return ClassReflectionInterface[]
     */
    public function getIndirectSubClasses(): array
    {
        $subClasses = [];
        foreach ($this->getParsedClasses() as $class) {
            if ($class->isDocumented() && $this->getName() !== $class->getParentClassName()
                && $class->isSubclassOf($this->getName())
            ) {
                $subClasses[] = $class;
            }
        }

        uksort($subClasses, 'strcasecmp');
        return $subClasses;
    }

    public function implementsInterface(string $interface): bool
    {
        return $this->reflection->implementsInterface($interface);
    }
    /**
     * @return ClassReflectionInterface[]
     */
    public function getInterfaces(): array
    {
        return array_map(function (IReflectionClass $class) {
            return $this->getParsedClasses()[$class->getName()];
        }, $this->reflection->getInterfaces());
    }

    /**
     * @return ClassReflectionInterface[]
     */
    public function getOwnInterfaces(): array
    {
        return array_map(function (IReflectionClass $class) {
            return $this->getParsedClasses()[$class->getName()];
        }, $this->reflection->getOwnInterfaces());
    }

    /**
     * @return string[]
     */
    public function getOwnInterfaceNames(): array
    {
        return $this->reflection->getOwnInterfaceNames();
    }


    public function getMethod(string $name): MethodReflectionInterface
    {
        if ($this->hasMethod($name)) {
            return $this->methods[$name];
        }

        throw new InvalidArgumentException(sprintf(
            'Method %s does not exist in class %s',
            $name,
            $this->reflection->getName()
        ));
    }

    /**
     * @return MethodReflectionInterface[]
     */
    public function getInheritedMethods(): array
    {
        return $this->parentClassElementExtractor->getInheritedMethods();
    }

    /**
     * @return MethodReflectionInterface[]
     */
    public function getUsedMethods(): array
    {
        $usedMethods = $this->classTraitElementExtractor->getUsedMethods();
        return $this->sortUsedMethods($usedMethods);
    }




    public function getMethod(string $name): MethodReflectionInterface
    {
        // TODO: Implement getMethod() method.
    }

    public function hasMethod(string $name): bool
    {
        return isset($this->getMethods()[$name]);
    }

    /**
     * @return ConstantReflectionInterface[]
     */
    public function getConstants(): array
    {
        if ($this->constants === null) {
            $this->constants = [];
            foreach ($this->reflection->getConstantReflections() as $constant) {
                $apiConstant = $this->transformerCollector->transformReflectionToElement($constant);
                if (! $this->isDocumented() || $apiConstant->isDocumented()) {
                    /** @var ReflectionElement $constant */
                    $this->constants[$constant->getName()] = $apiConstant;
                }
            }
        }

        return $this->constants;
    }

    /**
     * @return ConstantReflectionInterface[]
     */
    public function getOwnConstants(): array
    {
        if ($this->ownConstants === null) {
            $this->ownConstants = [];
            $className = $this->reflection->getName();
            foreach ($this->getConstants() as $constantName => $constant) {
                if ($className === $constant->getDeclaringClassName()) {
                    $this->ownConstants[$constantName] = $constant;
                }
            }
        }

        return $this->ownConstants;
    }

    /**
     * @return ConstantReflectionInterface[]
     */
    public function getInheritedConstants(): array
    {
        return $this->parentClassElementExtractor->getInheritedConstants();
    }

    public function hasConstant(string $name): bool
    {
        return isset($this->getConstants()[$name]);
    }

    public function getConstant(string $name): ConstantReflectionInterface
    {
        if (isset($this->getConstants()[$name])) {
            return $this->getConstants()[$name];
        }

        throw new InvalidArgumentException(sprintf(
            'Constant %s does not exist in class %s',
            $name,
            $this->reflection->getName()
        ));
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
    public function getTraits(): array
    {
        return array_map(function (IReflectionClass $class) {
            if (! isset($this->getParsedClasses()[$class->getName()])) {
                return $class->getName();
            }

            return $this->getParsedClasses()[$class->getName()];
        }, $this->reflection->getTraits());
    }

    /**
     * @return ClassReflectionInterface[]
     */
    public function getOwnTraits(): array
    {
        return array_map(function (IReflectionClass $class) {
            if (! isset($this->getParsedClasses()[$class->getName()])) {
                return $class->getName();
            }

            return $this->getParsedClasses()[$class->getName()];
        }, $this->reflection->getOwnTraits());
    }

    /**
     * @return string[]
     */
    public function getOwnTraitNames(): array
    {
        return $this->reflection->getOwnTraitNames();
    }

    /**
     * @return string[]
     */
    public function getTraitAliases(): array
    {
        return $this->reflection->getTraitAliases();
    }

    /**
     * @return PropertyReflectionInterface[]
     */
    public function getProperties(): array
    {
        if ($this->properties === null) {
            $this->properties = $this->getOwnProperties();
            foreach ($this->reflection->getProperties() as $property) {
                /** @var ReflectionElement $property */
                if (isset($this->properties[$property->getName()])) {
                    continue;
                }

                $apiProperty = $this->transformerCollector->transformSingle($property);
                if (! $this->isDocumented() || $apiProperty->isDocumented()) {
                    $this->properties[$property->getName()] = $apiProperty;
                }
            }
        }

        return $this->properties;
    }

    /**
     * @return PropertyReflectionInterface[]
     */
    public function getOwnProperties(): array
    {
        if ($this->ownProperties === null) {
            $this->ownProperties = [];
            foreach ($this->reflection->getOwnProperties() as $property) {
                $apiProperty = $this->transformerCollector->transformSingle($property);
                if (! $this->isDocumented() || $apiProperty->isDocumented()) {
                    /** @var ReflectionElement $property */
                    $this->ownProperties[$property->getName()] = $apiProperty;
                }
            }
        }

        return $this->ownProperties;
    }

    /**
     * @return PropertyReflectionInterface[]
     */
    public function getInheritedProperties(): array
    {
        return $this->parentClassElementExtractor->getInheriteProperties();
    }

    /**
     * @return PropertyReflectionInterface[]
     */
    public function getTraitProperties(): array
    {
        return $this->classTraitElementExtractor->getTraitProperties();
    }

    /**
     * @return PropertyReflectionInterface[][]
     */
    public function getUsedProperties(): array
    {
        return $this->classTraitElementExtractor->getUsedProperties();
    }

    public function getProperty(string $name): PropertyReflectionInterface
    {
        if ($this->hasProperty($name)) {
            return $this->properties[$name];
        }

        throw new InvalidArgumentException(sprintf(
            'Property %s does not exist in class %s',
            $name,
            $this->reflection->getName()
        ));
    }

    public function hasProperty(string $name): bool
    {
        return isset($this->getProperties()[$name]);
    }

    public function usesTrait(string $trait): bool
    {
        return $this->reflection->usesTrait($trait);
    }

    /**
     * @return ClassReflectionInterface[]
     */
    public function getParsedClasses(): array
    {
        // TODO: Implement getParsedClasses() method.
    }

    public function isAbstract(): bool
    {
        return $this->reflection->isAbstract();
    }

    public function isFinal(): bool
    {
        return $this->reflection->isFinal();
    }

    public function isSubclassOf(string $class): bool
    {
        return $this->reflection->isSubclassOf($class);
    }


    public function isDeprecated(): bool
    {
        // TODO: Implement isDeprecated() method.
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
        return $this->reflection->getFileName();
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
     * @return MethodReflectionInterface[]
     */
    public function getMethods(): array
    {
        if ($this->methods === null) {
            $this->methods = $this->getOwnMethods();

            foreach ($this->getOwnTraits() as $trait) {
                if (!$trait instanceof ReflectionClass) {
                    continue;
                }

                foreach ($trait->getOwnMethods() as $method) {
                    if (isset($this->methods[$method->getName()])) {
                        continue;
                    }

                    if (! $this->isDocumented() || $method->isDocumented()) {
                        $this->methods[$method->getName()] = $method;
                    }
                }
            }

            if ($this->getParentClassName() !== null) {
                foreach ($this->getParentClass()->getMethods() as $parentMethod) {
                    if (!isset($this->methods[$parentMethod->getName()])) {
                        $this->methods[$parentMethod->getName()] = $parentMethod;
                    }
                }
            }

            foreach ($this->getOwnInterfaces() as $interface) {
                foreach ($interface->getMethods() as $parentMethod) {
                    if (!isset($this->methods[$parentMethod->getName()])) {
                        $this->methods[$parentMethod->getName()] = $parentMethod;
                    }
                }
            }
        }

        return $this->methods;
    }

    /**
     * @return MethodReflectionInterface[]
     */
    public function getOwnMethods(): array
    {
        if ($this->ownMethods === null) {
            $this->ownMethods = [];

            foreach ($this->reflection->getOwnMethods() as $method) {
                $apiMethod = $this->transformerCollector->transformSingle($method);
                if (! $this->isDocumented() || $apiMethod->isDocumented()) {
                    $this->ownMethods[$method->getName()] = $apiMethod;
                }
            }
        }

        return $this->ownMethods;
    }

    /**
     * @return MethodReflectionInterface[]
     */
    public function getTraitMethods(): array
    {
        return $this->classTraitElementExtractor->getTraitMethods();
    }
}
