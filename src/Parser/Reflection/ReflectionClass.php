<?php declare(strict_types=1);

namespace ApiGen\Parser\Reflection;

use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ConstantReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\Extractors\ClassMagicElementsExtractorInterface;
use ApiGen\Contracts\Parser\Reflection\Extractors\ClassTraitElementsExtractorInterface;
use ApiGen\Contracts\Parser\Reflection\Extractors\ParentClassElementsExtractorInterface;
use ApiGen\Contracts\Parser\Reflection\Magic\MagicMethodReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\Magic\MagicPropertyReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\MethodReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\PropertyReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\TokenReflection\ReflectionFactoryInterface;
use ApiGen\Parser\Reflection\Extractors\ClassMagicElementsExtractor;
use ApiGen\Parser\Reflection\Extractors\ClassTraitElementsExtractor;
use ApiGen\Parser\Reflection\Extractors\ParentClassElementsExtractor;
use InvalidArgumentException;
use ReflectionProperty as Visibility;
use TokenReflection\IReflectionClass;

final class ReflectionClass extends ReflectionElement implements ClassReflectionInterface
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
     * @var ClassMagicElementsExtractorInterface
     */
    private $classMagicElementExtractor;

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
        $this->classMagicElementExtractor = new ClassMagicElementsExtractor($this);
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

            if (null !== $this->getParentClassName()) {
                foreach ($this->getParentClass()->getMethods() as $parentMethod) {
                    if (!isset($this->methods[$parentMethod->getName()])) {
                        $this->methods[$parentMethod->getName()] = $parentMethod;
                    }
                }
            }

            foreach ($this->getOwnInterfaces() as $interface) {
                foreach ($interface->getMethods(null) as $parentMethod) {
                    if (!isset($this->methods[$parentMethod->getName()])) {
                        $this->methods[$parentMethod->getName()] = $parentMethod;
                    }
                }
            }

            $this->methods = array_filter($this->methods, function (ReflectionMethod $method) {
                return $method->configuration->getVisibilityLevel() === $this->getVisibilityLevel();
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
                $apiMethod = $this->reflectionFactory->createFromReflection($method);
                if (! $this->isDocumented() || $apiMethod->isDocumented()) {
                    $this->ownMethods[$method->getName()] = $apiMethod;
                }
            }
        }

        return $this->ownMethods;
    }


    /**
     * @return MagicMethodReflectionInterface[]
     */
    public function getMagicMethods(): array
    {
        return $this->classMagicElementExtractor->getMagicMethods();
    }


    /**
     * @return MagicMethodReflectionInterface[]
     */
    public function getOwnMagicMethods(): array
    {
        return $this->classMagicElementExtractor->getOwnMagicMethods();
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
     * @return MagicPropertyReflectionInterface[]
     */
    public function getMagicProperties(): array
    {
        return $this->classMagicElementExtractor->getMagicProperties();
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

                $apiProperty = $this->reflectionFactory->createFromReflection($property);
                if (! $this->isDocumented() || $apiProperty->isDocumented()) {
                    $this->properties[$property->getName()] = $apiProperty;
                }
            }
        }

        return $this->properties;
    }


    /**
     * @return ReflectionPropertyMagic[]
     */
    public function getOwnMagicProperties(): array
    {
        return $this->classMagicElementExtractor->getOwnMagicProperties();
    }


    /**
     * @return PropertyReflectionInterface[]
     */
    public function getOwnProperties(): array
    {
        if ($this->ownProperties === null) {
            $this->ownProperties = [];
            foreach ($this->reflection->getOwnProperties($this->getVisibilityLevel()) as $property) {
                $apiProperty = $this->reflectionFactory->createFromReflection($property);
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
                $apiConstant = $this->reflectionFactory->createFromReflection($constant);
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
        if ($className = $this->reflection->getParentClassName()) {
            return $this->getParsedClasses()[$className];
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


    /**
     * @return string[]
     */
    public function getParentClassNameList(): array
    {
        return $this->reflection->getParentClassNameList();
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
            } else {
                return $this->getParsedClasses()[$class->getName()];
            }
        }, $this->reflection->getTraits());
    }


    /**
     * @return string[]
     */
    public function getTraitNames(): array
    {
        return $this->reflection->getTraitNames();
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
            } else {
                return $this->getParsedClasses()[$class->getName()];
            }
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
     * @return MagicMethodReflectionInterface[]
     */
    public function getInheritedMagicMethods(): array
    {
        return $this->classMagicElementExtractor->getInheritedMagicMethods();
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
     * @return MagicMethodReflectionInterface[]
     */
    public function getUsedMagicMethods(): array
    {
        $usedMethods = $this->classMagicElementExtractor->getUsedMagicMethods();
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
     * @return MagicPropertyReflectionInterface[]
     */
    public function getInheritedMagicProperties(): array
    {
        return $this->classMagicElementExtractor->getInheritedMagicProperties();
    }


    /**
     * @return PropertyReflectionInterface[]
     */
    public function getUsedProperties(): array
    {
        return $this->classTraitElementExtractor->getUsedProperties();
    }


    /**
     * @return MagicPropertyReflectionInterface[]
     */
    public function getUsedMagicProperties(): array
    {
        return $this->classMagicElementExtractor->getUsedMagicProperties();
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


    public function isVisibilityLevelPublic(): bool
    {
        return (bool) ($this->getVisibilityLevel() & Visibility::IS_PUBLIC);
    }


    public function getVisibilityLevel(): int
    {
        return $this->configuration->getVisibilityLevel();
    }


    public function getReflectionFactory(): ReflectionFactoryInterface
    {
        return $this->reflectionFactory;
    }

    /**
     * Removed, but for BC in Themes
     */
    public function isValid(): bool
    {
        return true;
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
