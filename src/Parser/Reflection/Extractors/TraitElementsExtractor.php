<?php declare(strict_types=1);

namespace ApiGen\Parser\Reflection\Extractors;

use ApiGen\Reflection\Contract\Reflection\Class_\ClassReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Extractors\ClassTraitElementsExtractorInterface;
use ApiGen\Reflection\Contract\Reflection\ClassMethodReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\ClassPropertyReflectionInterface;

final class TraitElementsExtractor implements ClassTraitElementsExtractorInterface
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
            if ($class->usesTrait($name) && ! in_array($name, $class->getOwnTraitNames())) {
                $users[] = $class;
            }
        }

        uksort($users, 'strcasecmp');
        return $users;
    }

    /**
     * @return ClassPropertyReflectionInterface[]
     */
    public function getTraitProperties(): array
    {
        $properties = [];
        $traitProperties = $this->originalReflection->getTraitProperties();
        foreach ($traitProperties as $property) {
            $apiProperty = $this->transformerCollector->transformSingle($property);
            $properties[$property->getName()] = $apiProperty;
        }

        return $properties;
    }

    /**
     * @return ClassMethodReflectionInterface[]
     */
    public function getTraitMethods(): array
    {
        $methods = [];
        foreach ($this->originalReflection->getTraitMethods() as $method) {
            $apiMethod = $this->transformerCollector->transformSingle($method);
            $methods[$method->getName()] = $apiMethod;
        }

        return $methods;
    }

    /**
     * @return ClassPropertyReflectionInterface[][]
     */
    public function getUsedProperties(): array
    {
        $allProperties = array_flip(array_map(function (ClassPropertyReflectionInterface $property) {
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
     * @return ClassMethodReflectionInterface[]
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

    private function wasMethodNameAliased(ClassMethodReflectionInterface $methodReflection): bool
    {
        return $methodReflection->getOriginalName() !== null
            && $methodReflection->getOriginalName() !== $methodReflection->getName();
    }
}
