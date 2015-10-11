<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Parser\Reflection;

use ApiGen\Configuration\ConfigurationOptions as CO;
use ApiGen\Contracts\Parser\Reflection\ClassConstantReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\Extractors\ClassMagicElementsExtractorInterface;
use ApiGen\Contracts\Parser\Reflection\Extractors\ClassTraitElementsExtractorInterface;
use ApiGen\Contracts\Parser\Reflection\Extractors\ParentClassElementsExtractorInterface;
use ApiGen\Contracts\Parser\Reflection\MethodReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\PropertyReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\TokenReflection\ReflectionFactoryInterface;
use ApiGen\Parser\Reflection\Extractors\ClassMagicElementsExtractor;
use ApiGen\Parser\Reflection\Extractors\ClassTraitElementsExtractor;
use ApiGen\Parser\Reflection\Extractors\ParentClassElementsExtractor;
use InvalidArgumentException;
use ReflectionProperty as Visibility;
use TokenReflection;
use TokenReflection\IReflectionClass;

class ReflectionClass extends ReflectionElement implements ClassReflectionInterface
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
     * @var ClassConstantReflectionInterface[]
     */
    private $constants;

    /**
     * @var ClassConstantReflectionInterface[]
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


    public function __construct($reflectionClass)
    {
        parent::__construct($reflectionClass);
        $this->classMagicElementExtractor = new ClassMagicElementsExtractor($this);
        $this->classTraitElementExtractor = new ClassTraitElementsExtractor($this, $reflectionClass);
        $this->parentClassElementExtractor = new ParentClassElementsExtractor($this);
    }


    /**
     * {@inheritdoc}
     */
    public function getShortName()
    {
        return $this->reflection->getShortName();
    }


    /**
     * {@inheritdoc}
     */
    public function isAbstract()
    {
        return $this->reflection->isAbstract();
    }


    /**
     * {@inheritdoc}
     */
    public function isFinal()
    {
        return $this->reflection->isFinal();
    }


    /**
     * {@inheritdoc}
     */
    public function isInterface()
    {
        return $this->reflection->isInterface();
    }


    /**
     * {@inheritdoc}
     */
    public function isException()
    {
        return $this->reflection->isException();
    }


    /**
     * {@inheritdoc}
     */
    public function isSubclassOf($class)
    {
        return $this->reflection->isSubclassOf($class);
    }


    /**
     * {@inheritdoc}
     */
    public function getMethods()
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
     * {@inheritdoc}
     */
    public function getOwnMethods()
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
     * {@inheritdoc}
     */
    public function getMagicMethods()
    {
        return $this->classMagicElementExtractor->getMagicMethods();
    }


    /**
     * {@inheritdoc}
     */
    public function getOwnMagicMethods()
    {
        return $this->classMagicElementExtractor->getOwnMagicMethods();
    }


    /**
     * {@inheritdoc}
     */
    public function getTraitMethods()
    {
        return $this->classTraitElementExtractor->getTraitMethods();
    }


    /**
     * {@inheritdoc}
     */
    public function getMethod($name)
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
     * {@inheritdoc}
     */
    public function getProperties()
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
     * {@inheritdoc}
     */
    public function getMagicProperties()
    {
        return $this->classMagicElementExtractor->getMagicProperties();
    }


    /**
     * @return ReflectionPropertyMagic[]
     */
    public function getOwnMagicProperties()
    {
        return $this->classMagicElementExtractor->getOwnMagicProperties();
    }


    /**
     * {@inheritdoc}
     */
    public function getOwnProperties()
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
     * {@inheritdoc}
     */
    public function getTraitProperties()
    {
        return $this->classTraitElementExtractor->getTraitProperties();
    }


    /**
     * {@inheritdoc}
     */
    public function getProperty($name)
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
     * {@inheritdoc}
     */
    public function getConstants()
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
     * {@inheritdoc}
     */
    public function getOwnConstants()
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
     * {@inheritdoc}
     */
    public function getConstantReflection($name)
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


    /**
     * {@inheritdoc}
     */
    public function getConstant($name)
    {
        return $this->getConstantReflection($name);
    }


    /**
     * {@inheritdoc}
     */
    public function hasConstant($name)
    {
        return isset($this->getConstants()[$name]);
    }


    /**
     * {@inheritdoc}
     */
    public function hasOwnConstant($name)
    {
        return isset($this->getOwnConstants()[$name]);
    }


    /**
     * {@inheritdoc}
     */
    public function getOwnConstant($name)
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


    /**
     * {@inheritdoc}
     */
    public function getParentClass()
    {
        if ($className = $this->reflection->getParentClassName()) {
            return $this->getParsedClasses()[$className];
        }
        return $className;
    }


    /**
     * {@inheritdoc}
     */
    public function getParentClassName()
    {
        return $this->reflection->getParentClassName();
    }


    /**
     * {@inheritdoc}
     */
    public function getParentClasses()
    {
        if ($this->parentClasses === null) {
            $this->parentClasses = array_map(function (IReflectionClass $class) {
                return $this->getParsedClasses()[$class->getName()];
            }, $this->reflection->getParentClasses());
        }
        return $this->parentClasses;
    }


    /**
     * {@inheritdoc}
     */
    public function getParentClassNameList()
    {
        return $this->reflection->getParentClassNameList();
    }


    /**
     * {@inheritdoc}
     */
    public function implementsInterface($interface)
    {
        return $this->reflection->implementsInterface($interface);
    }


    /**
     * {@inheritdoc}
     */
    public function getInterfaces()
    {
        return array_map(function (IReflectionClass $class) {
            return $this->getParsedClasses()[$class->getName()];
        }, $this->reflection->getInterfaces());
    }


    /**
     * {@inheritdoc}
     */
    public function getOwnInterfaces()
    {
        return array_map(function (IReflectionClass $class) {
            return $this->getParsedClasses()[$class->getName()];
        }, $this->reflection->getOwnInterfaces());
    }


    /**
     * {@inheritdoc}
     */
    public function getOwnInterfaceNames()
    {
        return $this->reflection->getOwnInterfaceNames();
    }


    /**
     * {@inheritdoc}
     */
    public function getTraits()
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
     * {@inheritdoc}
     */
    public function getTraitNames()
    {
        return $this->reflection->getTraitNames();
    }


    /**
     * {@inheritdoc}
     */
    public function getOwnTraitNames()
    {
        return $this->reflection->getOwnTraitNames();
    }


    /**
     * {@inheritdoc}
     */
    public function getTraitAliases()
    {
        return $this->reflection->getTraitAliases();
    }


    /**
     * {@inheritdoc}
     */
    public function getOwnTraits()
    {
        return array_map(function (IReflectionClass $class) {
            if (! isset($this->getParsedClasses()[$class->getName()])) {
                return $class->getName();

            } else {
                return $this->getParsedClasses()[$class->getName()];
            }
        }, $this->reflection->getOwnTraits());
    }


    /**
     * {@inheritdoc}
     */
    public function isTrait()
    {
        return $this->reflection->isTrait();
    }


    /**
     * {@inheritdoc}
     */
    public function usesTrait($trait)
    {
        return $this->reflection->usesTrait($trait);
    }


    /**
     * {@inheritdoc}
     */
    public function getDirectSubClasses()
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
     * {@inheritdoc}
     */
    public function getIndirectSubClasses()
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
     * {@inheritdoc}
     */
    public function getDirectImplementers()
    {
        if (! $this->isInterface()) {
            return [];
        }
        return $this->parserResult->getDirectImplementersOfInterface($this);
    }


    /**
     * {@inheritdoc}
     */
    public function getIndirectImplementers()
    {
        if (! $this->isInterface()) {
            return [];
        }
        return $this->parserResult->getIndirectImplementersOfInterface($this);
    }


    /**
     * {@inheritdoc}
     */
    public function getDirectUsers()
    {
        if (! $this->isTrait()) {
            return [];
        }
        return $this->classTraitElementExtractor->getDirectUsers();
    }


    /**
     * {@inheritdoc}
     */
    public function getIndirectUsers()
    {
        if (! $this->isTrait()) {
            return [];
        }
        return $this->classTraitElementExtractor->getIndirectUsers();
    }


    /**
     * {@inheritdoc}
     */
    public function getInheritedMethods()
    {
        return $this->parentClassElementExtractor->getInheritedMethods();
    }


    /**
     * {@inheritdoc}
     */
    public function getInheritedMagicMethods()
    {
        return $this->classMagicElementExtractor->getInheritedMagicMethods();
    }


    /**
     * {@inheritdoc}
     */
    public function getUsedMethods()
    {
        $usedMethods = $this->classTraitElementExtractor->getUsedMethods();
        return $this->sortUsedMethods($usedMethods);
    }


    /**
     * {@inheritdoc}
     */
    public function getUsedMagicMethods()
    {
        $usedMethods = $this->classMagicElementExtractor->getUsedMagicMethods();
        return $this->sortUsedMethods($usedMethods);
    }


    /**
     * {@inheritdoc}
     */
    public function getInheritedConstants()
    {
        return $this->parentClassElementExtractor->getInheritedConstants();
    }


    /**
     * {@inheritdoc}
     */
    public function getInheritedProperties()
    {
        return $this->parentClassElementExtractor->getInheritedProperties();
    }


    /**
     * {@inheritdoc}
     */
    public function getInheritedMagicProperties()
    {
        return $this->classMagicElementExtractor->getInheritedMagicProperties();
    }


    /**
     * {@inheritdoc}
     */
    public function getUsedProperties()
    {
        return $this->classTraitElementExtractor->getUsedProperties();
    }


    /**
     * {@inheritdoc}
     */
    public function getUsedMagicProperties()
    {
        return $this->classMagicElementExtractor->getUsedMagicProperties();
    }


    /**
     * {@inheritdoc}
     */
    public function hasProperty($name)
    {
        if ($this->properties === null) {
            $this->getProperties();
        }
        return isset($this->properties[$name]);
    }


    /**
     * {@inheritdoc}
     */
    public function hasMethod($name)
    {
        return isset($this->getMethods()[$name]);
    }


    /**
     * {@inheritdoc}
     */
    public function isValid()
    {
        if ($this->reflection instanceof TokenReflection\Invalid\ReflectionClass) {
            return false;
        }

        return true;
    }


    /**
     * {@inheritdoc}
     */
    public function isVisibilityLevelPublic()
    {
        return $this->getVisibilityLevel() & Visibility::IS_PUBLIC;
    }


    /**
     * @return int
     */
    public function getVisibilityLevel()
    {
        return $this->configuration->getVisibilityLevel();
    }


    /**
     * @return ReflectionFactoryInterface
     */
    public function getReflectionFactory()
    {
        return $this->reflectionFactory;
    }


    /**
     * @return array
     */
    private function sortUsedMethods(array $usedMethods)
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
