<?php declare(strict_types=1);

namespace ApiGen\Parser;

use ApiGen\Contracts\Parser\Elements\ElementsInterface;
use ApiGen\Contracts\Parser\ParserStorageInterface;
use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\FunctionReflectionInterface;
use Exception;

final class ParserStorage implements ParserStorageInterface
{
    /**
     * @var ClassReflectionInterface[]
     */
    private $classes = [];

    /**
     * @var FunctionReflectionInterface[]
     */
    private $functions = [];

    /**
     * @var int[]
     */
    private $types = [ElementsInterface::CLASSES, ElementsInterface::FUNCTIONS];

    /**
     * @return mixed[]
     */
    public function getElementsByType(string $type): array
    {
        if ($type === ElementsInterface::CLASSES) {
            return $this->classes;
        }

        if ($type === ElementsInterface::FUNCTIONS) {
            return $this->functions;
        }

        throw new Exception(sprintf(
            '"%s" is not supported element type. Pick one of: %s',
            $type,
            implode(',', $this->types)
        ));
    }

    /**
     * @return ClassReflectionInterface[]
     */
    public function getClasses(): array
    {
        return $this->classes;
    }

    /**
     * @return FunctionReflectionInterface[]
     */
    public function getFunctions(): array
    {
        return $this->functions;
    }

    /**
     * @return int[]
     */
    public function getTypes(): array
    {
        return $this->types;
    }

    /**
     * @param ClassReflectionInterface[] $classes
     */
    public function setClasses(array $classes): void
    {
        $this->classes = $classes;
    }

    /**
     * @param FunctionReflectionInterface[] $functions
     */
    public function setFunctions(array $functions): void
    {
        $this->functions = $functions;
    }

    /**
     * @return ClassReflectionInterface[]
     */
    public function getDirectImplementersOfInterface(ClassReflectionInterface $reflectionClass): array
    {
        $implementers = [];
        foreach ($this->classes as $class) {
            if ($this->isAllowedDirectImplementer($class, $reflectionClass->getName())) {
                $implementers[] = $class;
            }
        }

        uksort($implementers, 'strcasecmp');

        return $implementers;
    }

    /**
     * @return ClassReflectionInterface[]
     */
    public function getIndirectImplementersOfInterface(ClassReflectionInterface $reflectionClass): array
    {
        $implementers = [];
        foreach ($this->classes as $class) {
            if ($this->isAllowedIndirectImplementer($class, $reflectionClass->getName())) {
                $implementers[] = $class;
            }
        }

        uksort($implementers, 'strcasecmp');
        return $implementers;
    }

    private function isAllowedDirectImplementer(ClassReflectionInterface $class, string $name): bool
    {
        return $class->isDocumented() && in_array($name, $class->getOwnInterfaceNames());
    }

    private function isAllowedIndirectImplementer(ClassReflectionInterface $class, string $name): bool
    {
        return $class->isDocumented() && $class->implementsInterface($name)
            && ! in_array($name, $class->getOwnInterfaceNames());
    }
}
