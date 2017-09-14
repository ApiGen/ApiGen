<?php declare(strict_types=1);

namespace ApiGen\Annotation\FqsenResolver;

use ApiGen\Reflection\Contract\Reflection\AbstractReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Class_\AbstractClassElementInterface;
use ApiGen\Reflection\Contract\Reflection\Class_\ClassMethodReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Class_\ClassPropertyReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Class_\ClassReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Function_\FunctionReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Interface_\AbstractInterfaceElementInterface;
use ApiGen\Reflection\Contract\Reflection\Trait_\AbstractTraitElementInterface;
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
        $reflectionName = $this->getReflectionName($reflection);

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
            $namespace = $this->getNamespace($reflection);
            $functionReflections = $this->reflectionStorage->getFunctionReflections();

            $namespacedFunctionName = $namespace . '\\' . $functionName;

            return $functionReflections[$namespacedFunctionName] ?? $namespacedFunctionName;
        }

        $context = $this->contextFactory->createFromReflector(new ReflectionClass($reflectionName));

        $classReflectionName = (string) $this->fqsenResolver->resolve(ltrim($name, '\\'), $context);
        $classReflectionName = ltrim($classReflectionName, '\\');

        // @todo return only string on non resolved existing class
        /** @var ClassReflectionInterface|InterfaceReflectionInterface|TraitReflectionInterface */
        $classyReflection = $this->getClassyReflection($classReflectionName);

        if ($classyReflection === null) {
            // @todo or autoresolve class that exists?
            return $name;
        }

        if ($isProperty) {
            return $classyReflection->getProperty($propertyName);
        }

        if ($isMethod) {
            return $classyReflection->getMethod($methodName);
        }

        return $classyReflection;
    }

    private function getReflectionName(AbstractReflectionInterface $reflection): string
    {
        if ($reflection instanceof AbstractClassElementInterface) {
            return $reflection->getDeclaringClassName();
        } elseif ($reflection instanceof AbstractInterfaceElementInterface) {
            return $reflection->getDeclaringInterfaceName();
        } elseif ($reflection instanceof AbstractTraitElementInterface) {
            return $reflection->getDeclaringTraitName();
        }

        return $reflection->getName();
    }

    private function getClassyReflection(string $name): ?AbstractReflectionInterface
    {
        $classyReflections = $this->reflectionStorage->getClassReflections() +
            $this->reflectionStorage->getInterfaceReflections() +
            $this->reflectionStorage->getTraitReflections();

        foreach ($classyReflections as $reflection) {
            if ($reflection->getName() === $name) {
                return $reflection;
            }
        }

        return null;
    }

    private function getNamespace(AbstractReflectionInterface $reflection): ?string
    {
        if ($reflection instanceof AbstractClassElementInterface) {
            return $reflection->getDeclaringClass()->getNamespaceName();
        } elseif ($reflection instanceof AbstractInterfaceElementInterface) {
            return $reflection->getDeclaringInterface()->getNamespaceName();
        } elseif ($reflection instanceof AbstractTraitElementInterface) {
            return $reflection->getDeclaringTrait()->getNamespaceName();
        } elseif ($reflection instanceof FunctionReflectionInterface) {
            return $reflection->getNamespaceName();
        }

        return null;
    }
}
