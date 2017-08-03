<?php declare(strict_types=1);

namespace ApiGen\Annotation\FqsenResolver;

use ApiGen\Reflection\Contract\Reflection\AbstractReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Class_\ClassMethodReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Class_\ClassPropertyReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Class_\ClassReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Function_\FunctionReflectionInterface;
use ApiGen\Reflection\ReflectionStorage;
use Nette\Utils\Strings;
use phpDocumentor\Reflection\FqsenResolver;
use phpDocumentor\Reflection\Types\ContextFactory;
use ReflectionClass;

final class ElementResolver
{
    /**
     * @var ContextFactory
     */
    private $contextFactory;

    /**
     * @var FqsenResolver
     */
    private $fqsenResolver;

    /**
     * @var ReflectionStorage
     */
    private $reflectionStorage;

    public function __construct(
        ContextFactory $contextFactory,
        FqsenResolver $fqsenResolver,
        ReflectionStorage $reflectionStorage
    ) {
        $this->contextFactory = $contextFactory;
        $this->fqsenResolver = $fqsenResolver;
        $this->reflectionStorage = $reflectionStorage;
    }

    /**
     * @return ClassReflectionInterface|ClassPropertyReflectionInterface|ClassMethodReflectionInterface|FunctionReflectionInterface|string
     */
    public function resolveReflectionFromNameAndReflection(string $name, AbstractReflectionInterface $reflection)
    {
        $reflectionName = $reflection->getName();
        if ($reflection instanceof ClassMethodReflectionInterface) {
            $reflectionName = $reflection->getDeclaringClassName();
        }

        $isProperty = false;
        $propertyName = '';
        if (Strings::contains($name, '::$')) {
            [$name, $propertyName] = explode('::$', $name);
            $isProperty = true;
        }

        $isFunction = false;
        $isMethod = false;
        $methodName = '';
        $functionName = '';
        if (Strings::contains($name, '()')) {
            if (Strings::contains($name, '::')) {
                [$name, $methodName] = explode('::', $name);
                $methodName = rtrim($methodName, '()');
                $isMethod = true;
            } else {
                $functionName = rtrim($name, '()');
                $isFunction = true;
            }
        }

        if ($isFunction) {
            $namespace = $reflection->getDeclaringClass()->getNamespaceName();
            $functionReflections = $this->reflectionStorage->getFunctionReflections();

            $namespacedFunctionName = $namespace . '\\' . $functionName;
            return $functionReflections[$namespacedFunctionName] ?? $namespacedFunctionName;
        }

        $context = $this->contextFactory->createFromReflector(new ReflectionClass($reflectionName));

        $classReflectionName = (string) $this->fqsenResolver->resolve(ltrim($name, '\\'), $context);
        $classReflectionName = ltrim($classReflectionName, '\\');

        // @todo return only string on non resolved existing class
        $classReflections = $this->reflectionStorage->getClassReflections();

        if (! isset($classReflections[$classReflectionName])) {
            // @todo or autoresolve class that exists?
            return $name;
        }

        $classReflection = $classReflections[$classReflectionName];

        if ($isProperty) {
            return $classReflection->getProperty($propertyName);
        }

        if ($isMethod) {
            return $classReflection->getMethod($methodName);
        }

        return $classReflection;
    }
}
