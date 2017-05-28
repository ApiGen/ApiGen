<?php declare(strict_types=1);

namespace ApiGen\Annotation\FqsenResolver;

use ApiGen\Reflection\Contract\Reflection\AbstractReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Class_\ClassMethodReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Class_\ClassPropertyReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Class_\ClassReflectionInterface;
use ApiGen\Reflection\Contract\ReflectionStorageInterface;
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
     * @var ReflectionStorageInterface
     */
    private $reflectionStorage;

    public function __construct(
        ContextFactory $contextFactory,
        FqsenResolver $fqsenResolver,
        ReflectionStorageInterface $reflectionStorage
    ) {
        $this->contextFactory = $contextFactory;
        $this->fqsenResolver = $fqsenResolver;
        $this->reflectionStorage = $reflectionStorage;
    }

    /**
     * @return ClassReflectionInterface|ClassPropertyReflectionInterface
     */
    public function resolveReflectionFromNameAndReflection(string $name, AbstractReflectionInterface $reflection)
    {
        if ($reflection instanceof ClassMethodReflectionInterface) {
            $reflectionName = $reflection->getDeclaringClassName();
        }

        $isProperty = false;
        if (Strings::contains( $name, '::$')) {
            [$name, $property] = explode('::$', $name);
            $isProperty = true;
        }

        $context = $this->contextFactory->createFromReflector(new ReflectionClass($reflectionName));
        $classReflectionName = (string) $this->fqsenResolver->resolve(ltrim($name, '\\'), $context);
        $classReflectionName = ltrim($classReflectionName, '\\');

        $classReflection = $this->reflectionStorage->getClassReflections()[$classReflectionName];

        if ($isProperty) {
            return $classReflection->getProperty($property);
        }

        return $classReflection;
    }
}
