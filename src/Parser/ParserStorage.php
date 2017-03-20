<?php declare(strict_types=1);

namespace ApiGen\Parser;

use ApiGen\Contracts\Parser\Elements\ElementsInterface;
use ApiGen\Contracts\Parser\ParserStorageInterface;
use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ElementReflectionInterface;
use Exception;

final class ParserStorage implements ParserStorageInterface
{
    /**
     * @var array
     */
    private $classes = [];

    /**
     * @var array
     */
    private $constants = [];

    /**
     * @var array
     */
    private $functions = [];

    /**
     * @var array
     */
    private $types = [ElementsInterface::CLASSES, ElementsInterface::CONSTANTS, ElementsInterface::FUNCTIONS];


    public function getElementsByType(string $type): array
    {
        if ($type === ElementsInterface::CLASSES) {
            return $this->classes;
        } elseif ($type === ElementsInterface::CONSTANTS) {
            return $this->constants;
        } elseif ($type === ElementsInterface::FUNCTIONS) {
            return $this->functions;
        }

        throw new Exception(sprintf(
            '"%s" is not supported element type. Pick one of: %s',
            $type,
            implode(',', $this->types)
        ));
    }


    public function getDocumentedStats(): array
    {
        return [
            'classes' => $this->getDocumentedElementsCount($this->classes),
            'constants' => $this->getDocumentedElementsCount($this->constants),
            'functions' => $this->getDocumentedElementsCount($this->functions),
        ];
    }


    public function getClasses(): array
    {
        return $this->classes;
    }


    public function getConstants(): array
    {
        return $this->constants;
    }


    public function getFunctions(): array
    {
        return $this->functions;
    }


    public function getTypes(): array
    {
        return $this->types;
    }


    public function setClasses(array $classes): void
    {
        $this->classes = $classes;
    }


    public function setConstants(array $constants): void
    {
        $this->constants = $constants;
    }


    public function setFunctions(array $functions): void
    {
        $this->functions = $functions;
    }


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


    /**
     * @param ElementReflectionInterface[] $result
     */
    private function getDocumentedElementsCount(array $result): int
    {
        $count = 0;
        foreach ($result as $element) {
            $count += (int) $element->isDocumented();
        }

        return $count;
    }
}
