<?php declare(strict_types=1);

namespace ApiGen\Parser\Reflection;

use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ConstantReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\Extractors\ClassTraitElementsExtractorInterface;
use ApiGen\Contracts\Parser\Reflection\Extractors\ParentClassElementsExtractorInterface;
use ApiGen\Contracts\Parser\Reflection\MethodReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\PropertyReflectionInterface;
use ApiGen\Parser\Reflection\Extractors\ClassTraitElementsExtractor;
use ApiGen\Parser\Reflection\Extractors\ParentClassElementsExtractor;
use ApiGen\ReflectionToElementTransformer\Contract\TransformerCollectorInterface;
use InvalidArgumentException;
use TokenReflection\IReflectionClass;

final class ReflectionClass extends AbstractReflectionElement implements ClassReflectionInterface
{
    /**
     * @var ClassReflectionInterface[]
     */
    private $parentClasses;

    /**
     * @var PropertyReflectionInterface[]
     */
    private $properties;

    /**
     * @var PropertyReflectionInterface[]
     */
    private $ownProperties;

    /**
     * @var ConstantReflectionInterface[]
     */
    private $constants;

    /**
     * @var ConstantReflectionInterface[]
     */
    private $ownConstants;

    /**
     * @var MethodReflectionInterface[]
     */
    private $methods;

    /**
     * @var MethodReflectionInterface[]
     */
    private $ownMethods;

    /**
     * @var ClassTraitElementsExtractorInterface
     */
    private $classTraitElementExtractor;

    /**
     * @var ParentClassElementsExtractorInterface
     */
    private $parentClassElementExtractor;

    /**
     * @param mixed $reflectionClass
     */
    public function __construct($reflectionClass)
    {
        parent::__construct($reflectionClass);
        $this->classTraitElementExtractor = new ClassTraitElementsExtractor($this, $reflectionClass);
        $this->parentClassElementExtractor = new ParentClassElementsExtractor($this);
    }

    public function getShortName(): string
    {
        return $this->reflection->getShortName();
    }

    public function isAbstract(): bool
    {
        return $this->reflection->isAbstract();
    }

    public function isFinal(): bool
    {
        return $this->reflection->isFinal();
    }

    public function isInterface(): bool
    {
        return $this->reflection->isInterface();
    }

    public function isException(): bool
    {
        return $this->reflection->isException();
    }

    public function isSubclassOf(string $class): bool
    {
        return $this->reflection->isSubclassOf($class);
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

            $this->methods = array_filter($this->methods, function (ReflectionMethod $method) {
                return $method->configuration->getVisibilityLevels() === $this->getVisibilityLevel();
            });
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

            foreach ($this->reflection->getOwnMethods($this->getVisibilityLevel()) as $method) {
                $apiMethod = $this->transformerCollector->transformReflectionToElement($method);
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
     * @return PropertyReflectionInterface[]
     */
    public function getProperties(): array
    {
        if ($this->properties === null) {
            $this->properties = $this->getOwnProperties();
            foreach ($this->reflection->getProperties($this->getVisibilityLevel()) as $property) {
                /** @var ReflectionElement $property */
                if (isset($this->properties[$property->getName()])) {
                    continue;
                }

                $apiProperty = $this->transformerCollector->transformReflectionToElement($property);
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
            foreach ($this->reflection->getOwnProperties($this->getVisibilityLevel()) as $property) {
                $apiProperty = $this->transformerCollector->transformReflectionToElement($property);
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
    public function getTraitProperties(): array
    {
        return $this->classTraitElementExtractor->getTraitProperties();
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

    public function hasConstant(string $name): bool
    {
        return isset($this->getConstants()[$name]);
    }

    public function hasOwnConstant(string $name): bool
    {
        return isset($this->getOwnConstants()[$name]);
    }

    public function getOwnConstant(string $name): ConstantReflectionInterface
    {
        if (isset($this->getOwnConstants()[$name])) {
            return $this->getOwnConstants()[$name];
        }

        throw new InvalidArgumentException(sprintf(
            'Constant %s does not exist in class %s',
            $name,
            $this->reflection->getName()
        ));
    }

    public function getParentClass(): ?ClassReflectionInterface
    {
        $parentClassName = $this->reflection->getParentClassName();

        if ($parentClassName) {
            return $this->getParsedClasses()[$parentClassName];
        }

        return null;
    }

    public function getParentClassName(): ?string
    {
        return $this->reflection->getParentClassName();
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

    public function isTrait(): bool
    {
        return $this->reflection->isTrait();
    }

    public function usesTrait(string $trait): bool
    {
        return $this->reflection->usesTrait($trait);
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

    /**
     * @return ClassReflectionInterface[]
     */
    public function getDirectImplementers(): array
    {
        if (! $this->isInterface()) {
            return [];
        }

        return $this->parserStorage->getDirectImplementersOfInterface($this);
    }

    /**
     * @return ClassReflectionInterface[]
     */
    public function getIndirectImplementers(): array
    {
        if (! $this->isInterface()) {
            return [];
        }

        return $this->parserStorage->getIndirectImplementersOfInterface($this);
    }

    /**
     * @return ClassReflectionInterface[]
     */
    public function getDirectUsers(): array
    {
        if (! $this->isTrait()) {
            return [];
        }

        return $this->classTraitElementExtractor->getDirectUsers();
    }

    /**
     * @return ClassReflectionInterface[]
     */
    public function getIndirectUsers(): array
    {
        if (! $this->isTrait()) {
            return [];
        }

        return $this->classTraitElementExtractor->getIndirectUsers();
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

    /**
     * @return ConstantReflectionInterface[]
     */
    public function getInheritedConstants(): array
    {
        return $this->parentClassElementExtractor->getInheritedConstants();
    }

    /**
     * @return PropertyReflectionInterface[]
     */
    public function getInheritedProperties(): array
    {
        return $this->parentClassElementExtractor->getInheritedProperties();
    }

    /**
     * @return PropertyReflectionInterface[]
     */
    public function getUsedProperties(): array
    {
        return $this->classTraitElementExtractor->getUsedProperties();
    }

    public function hasProperty(string $name): bool
    {
        if ($this->properties === null) {
            $this->getProperties();
        }

        return isset($this->properties[$name]);
    }

    public function hasMethod(string $name): bool
    {
        return isset($this->getMethods()[$name]);
    }

    public function getVisibilityLevel(): int
    {
        return $this->configuration->getVisibilityLevels();
    }

    public function getTransformerCollector(): TransformerCollectorInterface
    {
        return $this->transformerCollector;
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
}
