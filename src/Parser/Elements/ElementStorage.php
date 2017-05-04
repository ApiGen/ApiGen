<?php declare(strict_types=1);

namespace ApiGen\Parser\Elements;

use ApiGen\Contracts\Parser\Elements\ElementStorageInterface;
use ApiGen\Contracts\Parser\Elements\NamespaceSorterInterface;
use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\FunctionReflectionInterface;

final class ElementStorage implements ElementStorageInterface
{
    /**
     * @var mixed[]
     */
    private $namespaces = [];

    /**
     * @var ClassReflectionInterface[]
     */
    private $classes = [];

    /**
     * @var ClassReflectionInterface[]
     */
    private $interfaces = [];

    /**
     * @var ClassReflectionInterface[]
     */
    private $traits = [];

    /**
     * @var ClassReflectionInterface[]
     */
    private $exceptions = [];

    /**
     * @var FunctionReflectionInterface[]
     */
    private $functions = [];

    /**
     * @var bool
     */
    private $areElementsCategorized = false;

    /**
     * @var NamespaceSorterInterface
     */
    private $groupSorter;

    public function __construct(NamespaceSorterInterface $groupSorter)
    {
        $this->groupSorter = $groupSorter;
    }

    /**
     * @return mixed[]
     */
    public function getNamespaces(): array
    {
        $this->ensureCategorization();
        return $this->namespaces;
    }

    /**
     * @return ClassReflectionInterface[]
     */
    public function getClasses(): array
    {
        $this->ensureCategorization();
        return $this->classes;
    }

    /**
     * @return ClassReflectionInterface[]
     */
    public function getInterfaces(): array
    {
        $this->ensureCategorization();
        return $this->interfaces;
    }

    /**
     * @return ClassReflectionInterface[]
     */
    public function getTraits(): array
    {
        $this->ensureCategorization();
        return $this->traits;
    }

    /**
     * @return ClassReflectionInterface[]
     */
    public function getExceptions(): array
    {
        $this->ensureCategorization();
        return $this->exceptions;
    }

    /**
     * @return FunctionReflectionInterface[]
     */
    public function getFunctions(): array
    {
         $this->ensureCategorization();
         return $this->functions;
    }

    // this is already done in Transformer, just once
    private function categorizeParsedElements(): void
    {
        foreach ($this->parserStorage->getTypes() as $type) {
            $elements = $this->parserStorage->getElementsByType($type);
            foreach ($elements as $elementName => $element) {
//                if (! $element->isDocumented()) {
//                    continue;
//                }

                // @todo: move somewhere else, ReflectionRepository?
                $this->categorizeElementToNamespace($elementType, $element);
            }
        }

        $this->sortNamespaces();
        $this->areElementsCategorized = true;
    }

    private function categorizeElementToNamespace(string $elementType, ReflectionInterface $element): void
    {
        $namespaceName = $element->getPseudoNamespaceName();

        $this->namespaces[$namespaceName][$elementType][$element->getShortName()] = $element;
    }

    private function sortNamespaces(): void
    {
        $this->namespaces = $this->groupSorter->sort($this->namespaces);
    }

    private function ensureCategorization(): void
    {
        if ($this->areElementsCategorized === false) {
            $this->categorizeParsedElements();
        }
    }
}
