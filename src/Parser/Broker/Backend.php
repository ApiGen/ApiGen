<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Parser\Broker;

use ApiGen\Contracts\Parser\Broker\BackendInterface;
use ApiGen\Contracts\Parser\Reflection\Behavior\InNamespaceInterface;
use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\MethodReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\TokenReflection\ReflectionFactoryInterface;
use ApiGen\Parser\Reflection\ReflectionClass;
use ApiGen\Parser\Reflection\ReflectionFunction;
use ApiGen\Parser\Reflection\ReflectionMethod;
use TokenReflection;
use TokenReflection\Broker;
use TokenReflection\IReflectionConstant;
use TokenReflection\IReflectionFunction;
use TokenReflection\Resolver;

/**
 * Customized TokenReflection broker backend.
 * Adds internal classes from @param, @var, @return, @throws annotations as well
 * as parent classes to the overall class list.
 *
 * @method TokenReflection\ReflectionNamespace[] getNamespaces()
 */
class Backend extends Broker\Backend\Memory implements BackendInterface
{

    /**
     * @var ClassReflectionInterface[][]
     */
    private $allClasses = [
        self::TOKENIZED_CLASSES => [],
        self::INTERNAL_CLASSES => [],
        self::NONEXISTENT_CLASSES => []
    ];

    /**
     * @var array
     */
    private $declared = [];

    /**
     * @var ReflectionFactoryInterface
     */
    private $reflectionFactory;


    public function __construct(ReflectionFactoryInterface $reflectionFactory)
    {
        $this->reflectionFactory = $reflectionFactory;
    }


    /**
     * {@inheritdoc}
     */
    public function getConstants()
    {
        return array_map(function (IReflectionConstant $constant) {
            return $this->reflectionFactory->createFromReflection($constant);
        }, parent::getConstants());
    }


    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array_map(function (IReflectionFunction $function) {
            return $this->reflectionFactory->createFromReflection($function);
        }, parent::getFunctions());
    }


    /**
     * @return ClassReflectionInterface[]
     */
    protected function parseClassLists()
    {
        $this->declared = array_flip(array_merge(get_declared_classes(), get_declared_interfaces()));

        foreach ($this->getNamespaces() as $namespace) {
            foreach ($namespace->getClasses() as $name => $ref) {
                $class = $this->reflectionFactory->createFromReflection($ref);

                $this->allClasses[self::TOKENIZED_CLASSES][$name] = $class;
                if (! $class->isDocumented()) {
                    continue;
                }

                $this->loadParentClassesAndInterfacesFromClassReflection($ref);
            }
        }

        foreach ($this->allClasses[self::TOKENIZED_CLASSES] as $class) {
            if (! $class->isDocumented()) {
                continue;
            }

            foreach ($class->getOwnMethods() as $method) {
                $this->processFunction($method);
            }

            foreach ($class->getOwnProperties() as $property) {
                $this->loadAnnotationFromReflection($class, $property->getAnnotations(), 'var');
            }
        }

        foreach ($this->getFunctions() as $function) {
            $this->processFunction($function);
        }

        array_walk_recursive($this->allClasses, function (&$reflection) {
            if (! $reflection instanceof ReflectionClass) {
                $reflection = $this->reflectionFactory->createFromReflection($reflection);
            }
        });

        return $this->allClasses;
    }


    /**
     * Processes a function/method and adds classes from annotations to the overall class array.
     *
     * @param ReflectionMethod|ReflectionFunction $reflection
     */
    private function processFunction($reflection)
    {
        $annotations = $reflection->getAnnotations();
        foreach (['param', 'return', 'throws'] as $annotation) {
            $this->loadAnnotationFromReflection($reflection, $annotations, $annotation);
        }

        foreach ($reflection->getParameters() as $parameter) {
            if ($hint = $parameter->getClassName()) {
                $this->addClass($hint);
            }
        }
    }


    /**
     * @param string $name
     */
    private function addClass($name)
    {
        $name = ltrim($name, '\\');

        if (! isset($this->declared[$name]) || $this->isClassLoaded($name)) {
            return false;
        }

        $parameterClass = $this->getBroker()->getClass($name);

        if ($parameterClass->isInternal()) {
            $this->allClasses[self::INTERNAL_CLASSES][$name] = $parameterClass;
            $parentClasses = array_merge($parameterClass->getInterfaces(), $parameterClass->getParentClasses());
            foreach ($parentClasses as $parentClass) {
                if (! isset($this->allClasses[self::INTERNAL_CLASSES][$parentName = $parentClass->getName()])) {
                    $this->allClasses[self::INTERNAL_CLASSES][$parentName] = $parentClass;
                }
            }

        } elseif (! $parameterClass->isTokenized()) {
            if (! isset($this->allClasses[self::NONEXISTENT_CLASSES][$name])) {
                $this->allClasses[self::NONEXISTENT_CLASSES][$name] = $parameterClass;
            }
        }
    }


    /**
     * @param TokenReflection\ReflectionClass|TokenReflection\Invalid\ReflectionClass $reflection
     */
    private function loadParentClassesAndInterfacesFromClassReflection($reflection)
    {
        $reflectionRelatedClassElements = array_merge($reflection->getParentClasses(), $reflection->getInterfaces());
        foreach ($reflectionRelatedClassElements as $parentName => $parentReflection) {
            /** @var TokenReflection\ReflectionClass $parentReflection */
            if ($parentReflection->isInternal()) {
                if (! isset($this->allClasses[self::INTERNAL_CLASSES][$parentName])) {
                    $this->allClasses[self::INTERNAL_CLASSES][$parentName] = $parentReflection;
                }

            } elseif (! $parentReflection->isTokenized()) {
                if (! isset($this->allClasses[self::NONEXISTENT_CLASSES][$parentName])) {
                    $this->allClasses[self::NONEXISTENT_CLASSES][$parentName] = $parentReflection;
                }
            }
        }
    }


    /**
     * @param string $name
     * @return bool
     */
    private function isClassLoaded($name)
    {
        return isset($this->allClasses[self::TOKENIZED_CLASSES][$name])
            || isset($this->allClasses[self::INTERNAL_CLASSES][$name])
            || isset($this->allClasses[self::NONEXISTENT_CLASSES][$name]);
    }


    /**
     * @param ClassReflectionInterface|MethodReflectionInterface $reflection
     * @param array $annotations
     * @param string $name
     */
    private function loadAnnotationFromReflection($reflection, array $annotations, $name)
    {
        if (! isset($annotations[$name])) {
            return;
        }

        foreach ($annotations[$name] as $doc) {
            foreach (explode('|', preg_replace('~\\s.*~', '', $doc)) as $name) {
                if ($name = rtrim($name, '[]')) {
                    $name = $this->getClassFqn($name, $reflection);
                    $this->addClass($name);
                }
            }
        }
    }


    /**
     * @param string $name
     * @param ClassReflectionInterface|MethodReflectionInterface|InNamespaceInterface $reflection
     * @return string
     */
    private function getClassFqn($name, $reflection)
    {
        return Resolver::resolveClassFqn($name, $reflection->getNamespaceAliases(), $reflection->getNamespaceName());
    }
}
