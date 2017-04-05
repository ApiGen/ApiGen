<?php declare(strict_types=1);

namespace ApiGen\Parser\Broker;

use ApiGen\Contracts\Parser\Reflection\Behavior\InNamespaceInterface;
use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ConstantReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\FunctionReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\MethodReflectionInterface;
use ApiGen\Parser\Reflection\ReflectionClass;
use ApiGen\Parser\Reflection\ReflectionFunction;
use ApiGen\Parser\Reflection\ReflectionMethod;
use ApiGen\ReflectionToElementTransformer\Contract\TransformerCollectorInterface;
use TokenReflection;
use TokenReflection\Broker\Backend\Memory;
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
final class Backend extends Memory
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
     * @var mixed[]
     */
    private $declared = [];

    /**
     * @var TransformerCollectorInterface
     */
    private $transformerCollector;

    public function __construct(TransformerCollectorInterface $transformerCollector)
    {
        $this->transformerCollector = $transformerCollector;
    }

    /**
     * @return FunctionReflectionInterface[]
     */
    public function getFunctions(): array
    {
        return array_map(function (IReflectionFunction $function) {
            return $this->transformerCollector->transformReflectionToElement($function);
        }, parent::getFunctions());
    }

    /**
     * @return ClassReflectionInterface[]
     */
    protected function parseClassLists(): array
    {
        $this->declared = array_flip(array_merge(
            get_declared_classes(),
            get_declared_interfaces(),
            get_declared_traits()
        ));

        foreach ($this->getNamespaces() as $namespace) {
            foreach ($namespace->getClasses() as $name => $ref) {
                $class = $this->transformerCollector->transformReflectionToElement($ref);

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
                $reflection = $this->transformerCollector->transformReflectionToElement($reflection);
            }
        });

        return $this->allClasses;
    }

    /**
     * Processes a function/method and adds classes from annotations to the overall class array.
     *
     * @param ReflectionMethod|ReflectionFunction $reflection
     */
    private function processFunction($reflection): void
    {
        $annotations = $reflection->getAnnotations();
        foreach (['param', 'return', 'throws'] as $annotation) {
            $this->loadAnnotationFromReflection($reflection, $annotations, $annotation);
        }

        foreach ($reflection->getParameters() as $parameter) {
            $hint = $parameter->getClassName();
            if ($hint) {
                $this->addClass($hint);
            }
        }
    }

    /**
     * @return false|void
     */
    private function addClass(string $name)
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
                $parentName = $parentClass->getName();

                if (! isset($this->allClasses[self::INTERNAL_CLASSES][$parentName])) {
                    $this->allClasses[self::INTERNAL_CLASSES][$parentName] = $parentClass;
                }
            }
        }
    }

    /**
     * @param TokenReflection\ReflectionClass|TokenReflection\Invalid\ReflectionClass $reflection
     */
    private function loadParentClassesAndInterfacesFromClassReflection($reflection): void
    {
        $reflectionRelatedClassElements = array_merge($reflection->getParentClasses(), $reflection->getInterfaces());
        foreach ($reflectionRelatedClassElements as $parentName => $parentReflection) {
            /** @var TokenReflection\ReflectionClass $parentReflection */
            if ($parentReflection->isInternal()) {
                if (! isset($this->allClasses[self::INTERNAL_CLASSES][$parentName])) {
                    $this->allClasses[self::INTERNAL_CLASSES][$parentName] = $parentReflection;
                }
            }
        }
    }

    private function isClassLoaded(string $name): bool
    {
        return isset($this->allClasses[self::TOKENIZED_CLASSES][$name])
            || isset($this->allClasses[self::INTERNAL_CLASSES][$name])
            || isset($this->allClasses[self::NONEXISTENT_CLASSES][$name]);
    }

    /**
     * @param ClassReflectionInterface|MethodReflectionInterface $reflection
     * @param mixed[] $annotations
     * @param string $name
     */
    private function loadAnnotationFromReflection($reflection, array $annotations, $name): void
    {
        if (! isset($annotations[$name])) {
            return;
        }

        foreach ($annotations[$name] as $doc) {
            foreach (explode('|', preg_replace('~\\s.*~', '', $doc)) as $name) {
                $name = rtrim($name, '[]');
                if ($name) {
                    $name = $this->getClassFqn($name, $reflection);
                    $this->addClass($name);
                }
            }
        }
    }

    /**
     * @param string $name
     * @param ClassReflectionInterface|MethodReflectionInterface|InNamespaceInterface $reflection
     */
    private function getClassFqn(string $name, $reflection): string
    {
        return Resolver::resolveClassFQN(
            $name,
            [],
            $reflection->getNamespaceName()
        );
    }
}
