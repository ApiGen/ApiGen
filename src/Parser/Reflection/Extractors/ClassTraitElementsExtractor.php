<?php declare(strict_types=1);

namespace ApiGen\Parser\Reflection\Extractors;

use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\Extractors\ClassTraitElementsExtractorInterface;
use ApiGen\Contracts\Parser\Reflection\MethodReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\PropertyReflectionInterface;
use TokenReflection\IReflection;

final class ClassTraitElementsExtractor implements ClassTraitElementsExtractorInterface
{
    /**
     * @var ClassReflectionInterface
     */
    private $classReflection;

    /**
     * @var \TokenReflection\IReflection|ClassReflectionInterface
     */
    private $originalReflection;

    public function __construct(ClassReflectionInterface $classReflection, IReflection $originalReflection)
    {
        $this->classReflection = $classReflection;
        $this->originalReflection = $originalReflection;
    }

    /**
     * @return ClassReflectionInterface[]
     */
    public function getDirectUsers(): array
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
     * @return ClassReflectionInterface[]
     */
    public function getIndirectUsers(): array
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
     * @return PropertyReflectionInterface[]
     */
    public function getTraitProperties(): array
    {
        $properties = [];
        $traitProperties = $this->originalReflection->getTraitProperties($this->classReflection->getVisibilityLevel());
        foreach ($traitProperties as $property) {
            $apiProperty = $this->classReflection->getTransformerCollector()->transformReflectionToElement($property);
            if (! $this->classReflection->isDocumented() || $apiProperty->isDocumented()) {
                $properties[$property->getName()] = $apiProperty;
            }
        }

        return $properties;
    }

    /**
     * @return MethodReflectionInterface[]
     */
    public function getTraitMethods(): array
    {
        $methods = [];
        foreach ($this->originalReflection->getTraitMethods($this->classReflection->getVisibilityLevel()) as $method) {
            $apiMethod = $this->classReflection->getTransformerCollector()->transformReflectionToElement($method);
            if (! $this->classReflection->isDocumented() || $apiMethod->isDocumented()) {
                $methods[$method->getName()] = $apiMethod;
            }
        }

        return $methods;
    }

    /**
     * @return PropertyReflectionInterface[][]
     */
    public function getUsedProperties(): array
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
     * @return MethodReflectionInterface[]
     */
    public function getUsedMethods(): array
    {
        $usedMethods = [];
        foreach ($this->classReflection->getMethods() as $methodReflection) {
            if ($methodReflection->getDeclaringTraitName() === ''
                || $methodReflection->getDeclaringTraitName() === $this->classReflection->getName()
            ) {
                continue;
            }

            $traitName = $methodReflection->getDeclaringTraitName();
            $methodName = $methodReflection->getName();

            $usedMethods[$traitName][$methodName]['method'] = $methodReflection;
            if ($this->wasMethodNameAliased($methodReflection)) {
                $usedMethods[$traitName][$methodName]['aliases'][$methodReflection->getName()] = $methodReflection;
            }
        }

        return $usedMethods;
    }

    private function wasMethodNameAliased(MethodReflectionInterface $methodReflection): bool
    {
        return $methodReflection->getOriginalName() !== null
            && $methodReflection->getOriginalName() !== $methodReflection->getName();
    }
}
