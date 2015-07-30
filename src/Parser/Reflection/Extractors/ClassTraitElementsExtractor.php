<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Parser\Reflection\Extractors;

use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\Extractors\ClassTraitElementsExtractorInterface;
use ApiGen\Contracts\Parser\Reflection\PropertyReflectionInterface;
use TokenReflection\IReflection;

class ClassTraitElementsExtractor implements ClassTraitElementsExtractorInterface
{

    /**
     * @var ClassReflectionInterface
     */
    private $classReflection;

    /**
     * @var \TokenReflection\IReflection|\ClassReflectionInterface
     */
    private $originalReflection;


    public function __construct(ClassReflectionInterface $classReflection, IReflection $originalReflection)
    {
        $this->classReflection = $classReflection;
        $this->originalReflection = $originalReflection;
    }


    /**
     * {@inheritdoc}
     */
    public function getDirectUsers()
    {
        $users = [];
        $name = $this->classReflection->getName();
        foreach ($this->classReflection->getParsedClasses() as $class) {
            if (! $class->isDocumented()) {
                continue;
            }
            if (in_array($name, $class->getOwnTraitNames())) {
                $users[] = $class;
            }
        }
        uksort($users, 'strcasecmp');
        return $users;
    }


    /**
     * {@inheritdoc}
     */
    public function getIndirectUsers()
    {
        $users = [];
        $name = $this->classReflection->getName();
        foreach ($this->classReflection->getParsedClasses() as $class) {
            if (! $class->isDocumented()) {
                continue;
            }
            if ($class->usesTrait($name) && ! in_array($name, $class->getOwnTraitNames())) {
                $users[] = $class;
            }
        }
        uksort($users, 'strcasecmp');
        return $users;
    }


    /**
     * {@inheritdoc}
     */
    public function getTraitProperties()
    {
        $properties = [];
        $traitProperties = $this->originalReflection->getTraitProperties($this->classReflection->getVisibilityLevel());
        foreach ($traitProperties as $property) {
            $apiProperty = $this->classReflection->getReflectionFactory()->createFromReflection($property);
            if (! $this->classReflection->isDocumented() || $apiProperty->isDocumented()) {
                $properties[$property->getName()] = $apiProperty;
            }
        }
        return $properties;
    }


    /**
     * {@inheritdoc}
     */
    public function getTraitMethods()
    {
        $methods = [];
        foreach ($this->originalReflection->getTraitMethods($this->classReflection->getVisibilityLevel()) as $method) {
            $apiMethod = $this->classReflection->getReflectionFactory()->createFromReflection($method);
            if (! $this->classReflection->isDocumented() || $apiMethod->isDocumented()) {
                $methods[$method->getName()] = $apiMethod;
            }
        }
        return $methods;
    }


    /**
     * {@inheritdoc}
     */
    public function getUsedProperties()
    {
        $allProperties = array_flip(array_map(function (PropertyReflectionInterface $property) {
            return $property->getName();
        }, $this->classReflection->getOwnProperties()));

        $properties = [];
        foreach ($this->classReflection->getTraits() as $trait) {
            if (! $trait instanceof ClassReflectionInterface) {
                continue;
            }

            $usedProperties = [];
            foreach ($trait->getOwnProperties() as $property) {
                if (! array_key_exists($property->getName(), $allProperties)) {
                    $usedProperties[$property->getName()] = $property;
                    $allProperties[$property->getName()] = null;
                }
            }

            if (! empty($usedProperties)) {
                ksort($usedProperties);
                $properties[$trait->getName()] = array_values($usedProperties);
            }
        }
        return $properties;
    }


    /**
     * {@inheritdoc}
     */
    public function getUsedMethods()
    {
        $usedMethods = [];
        foreach ($this->classReflection->getMethods() as $m) {
            if ($m->getDeclaringTraitName() === null
                || $m->getDeclaringTraitName() === $this->classReflection->getName()
            ) {
                continue;
            }

            $usedMethods[$m->getDeclaringTraitName()][$m->getName()]['method'] = $m;
            if ($m->getOriginalName() !== null && $m->getOriginalName() !== $m->getName()) {
                $usedMethods[$m->getDeclaringTraitName()][$m->getName()]['aliases'][$m->getName()] = $m;
            }
        }
        return $usedMethods;
    }
}
