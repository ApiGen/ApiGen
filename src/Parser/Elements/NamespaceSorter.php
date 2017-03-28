<?php declare(strict_types=1);

namespace ApiGen\Parser\Elements;

use ApiGen\Contracts\Configuration\ConfigurationInterface;
use ApiGen\Contracts\Parser\Elements\ElementsInterface;
use ApiGen\Contracts\Parser\Elements\NamespaceSorterInterface;

final class NamespaceSorter implements NamespaceSorterInterface
{
    /**
     * @var mixed[]
     */
    private $namespaces;

    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     * @var ElementsInterface
     */
    private $elements;

    public function __construct(ElementsInterface $elements, ConfigurationInterface $configuration)
    {
        $this->elements = $elements;
        $this->configuration = $configuration;
    }

    /**
     * @param mixed[] $namespaces
     * @return mixed[]
     */
    public function sort(array $namespaces): array
    {
        if ($this->isNoneOnly($namespaces)) {
            return [];
        }

        $this->namespaces = $namespaces;

        $namespaceNames = array_keys($namespaces);

        foreach ($namespaceNames as $namespaceName) {
            $this->addMissingParentNamespaces($namespaceName);
            $this->addMissingElementTypes($namespaceName);
        }

        uksort($this->namespaces, function ($one, $two) {
            return $this->compareNamespaceNames($one, $two, $this->configuration->getMain());
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

    private function compareNamespaceNames(string $firstNamespace, string $secondNamespace, string $main): int
    {
        // \ as separator has to be first
        $firstNamespace = str_replace('\\', ' ', $firstNamespace);
        $secondNamespace = str_replace('\\', ' ', $secondNamespace);

        // @todo: battle ship?
        if ($main) {
            if (strpos($firstNamespace, $main) === 0 && strpos($secondNamespace, $main) !== 0) {
                return -1;
            } elseif (strpos($firstNamespace, $main) !== 0 && strpos($secondNamespace, $main) === 0) {
                return 1;
            }
        }

        return strcasecmp($firstNamespace, $secondNamespace);
    }
}
