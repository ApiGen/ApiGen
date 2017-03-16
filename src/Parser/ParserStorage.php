<?php declare(strict_types=1);

namespace ApiGen\Parser;

use ApiGen\Contracts\Parser\Elements\ElementsInterface;
use ApiGen\Contracts\Parser\ParserStorageInterface;
use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ElementReflectionInterface;
use ArrayObject;

class ParserStorage implements ParserStorageInterface
{

    /**
     * @var ArrayObject
     */
    private $classes;

    /**
     * @var ArrayObject
     */
    private $constants;

    /**
     * @var ArrayObject
     */
    private $functions;

    /**
     * @var ArrayObject
     */
    private $internalClasses;

    /**
     * @var ArrayObject
     */
    private $tokenizedClasses;

    /**
     * @var array
     */
    private $types = [ElementsInterface::CLASSES, ElementsInterface::CONSTANTS, ElementsInterface::FUNCTIONS];


    public function __construct()
    {
        $this->classes = new ArrayObject;
        $this->constants = new ArrayObject;
        $this->functions = new ArrayObject;
        $this->internalClasses = new ArrayObject;
        $this->tokenizedClasses = new ArrayObject;
    }


    public function getElementsByType(string $type): ArrayObject
    {
        if ($type === ElementsInterface::CLASSES) {
            return $this->classes;
        } elseif ($type === ElementsInterface::CONSTANTS) {
            return $this->constants;
        } elseif ($type === ElementsInterface::FUNCTIONS) {
            return $this->functions;
        }

        throw new \Exception(sprintf(
            '"%s" is not supported element type',
            $type
        ));
    }


    public function getDocumentedStats(): array
    {
        return [
            'classes' => $this->getDocumentedElementsCount($this->tokenizedClasses),
            'constants' => $this->getDocumentedElementsCount($this->constants),
            'functions' => $this->getDocumentedElementsCount($this->functions),
            'internalClasses' => $this->getDocumentedElementsCount($this->internalClasses)
        ];
    }


    public function getClasses(): ArrayObject
    {
        return $this->classes;
    }


    public function getConstants(): ArrayObject
    {
        return $this->constants;
    }


    public function getFunctions(): ArrayObject
    {
        return $this->functions;
    }


    public function getTypes(): array
    {
        return $this->types;
    }


    public function setClasses(ArrayObject $classes): void
    {
        $this->classes = $classes;
    }


    public function setConstants(ArrayObject $constants): void
    {
        $this->constants = $constants;
    }


    public function setFunctions(ArrayObject $functions): void
    {
        $this->functions = $functions;
    }


    public function setInternalClasses(ArrayObject $internalClasses): void
    {
        $this->internalClasses = $internalClasses;
    }


    public function setTokenizedClasses(ArrayObject $tokenizedClasses): void
    {
        $this->tokenizedClasses = $tokenizedClasses;
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
        if ($class->isDocumented() && $class->implementsInterface($name)
            && ! in_array($name, $class->getOwnInterfaceNames())
        ) {
            return true;
        }
        return false;
    }


    /**
     * @param ElementReflectionInterface[] $result
     */
    private function getDocumentedElementsCount(ArrayObject $result): int
    {
        $count = 0;
        foreach ($result as $element) {
            $count += (int) $element->isDocumented();
        }
        return $count;
    }
}
