<?php declare(strict_types=1);

namespace ApiGen\Parser\Elements;

use ApiGen\Contracts\Parser\Elements\ElementsInterface;
use ApiGen\Contracts\Parser\Elements\NamespaceSorterInterface;

final class NamespaceSorter implements NamespaceSorterInterface
{
    /**
     * @var mixed[]
     */
    private $namespaces;

    /**
     * @var ElementsInterface
     */
    private $elements;

    public function __construct(ElementsInterface $elements)
    {
        $this->elements = $elements;
    }

    /**
     * @param mixed[] $namespaces
     * @return mixed[]
     */
    public function sort(array $namespaces): array
    {
        if ($this->isNoneOnly($namespaces)) {
            return $namespaces;
        }

        $this->namespaces = $namespaces;

        $namespaceNames = array_keys($namespaces);

        foreach ($namespaceNames as $namespaceName) {
            $this->addMissingParentNamespaces($namespaceName);
            $this->addMissingElementTypes($namespaceName);
        }

        uksort($this->namespaces, function ($one, $two) {
            return $this->compareNamespaceNames($one, $two);
        });

        return $this->namespaces;
    }

    /**
     * @param mixed[] $namespaces
     */
    private function isNoneOnly(array $namespaces): bool
    {
        return count($namespaces) === 1 && isset($namespaces['None']);
    }

    private function addMissingParentNamespaces(string $namespaceName): void
    {
        $parent = '';
        foreach (explode('\\', $namespaceName) as $part) {
            $parent = ltrim($parent . '\\' . $part, '\\');

            if (! isset($this->namespaces[$parent])) {
                $this->namespaces[$parent] = $this->elements->getEmptyList();
            }
        }
    }

    private function addMissingElementTypes(string $namespaceName): void
    {
        foreach ($this->elements->getAll() as $type) {
            if (! isset($this->namespaces[$namespaceName][$type])) {
                $this->namespaces[$namespaceName][$type] = [];
            }
        }
    }

    private function compareNamespaceNames(string $firstNamespace, string $secondNamespace): int
    {
        // \ as separator has to be first
        $firstNamespace = str_replace('\\', ' ', $firstNamespace);
        $secondNamespace = str_replace('\\', ' ', $secondNamespace);

        return strcasecmp($firstNamespace, $secondNamespace);
    }
}
