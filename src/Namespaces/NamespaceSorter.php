<?php declare(strict_types=1);

namespace ApiGen\Namespaces;

use ApiGen\Parser\ElementEmptyListFactory;

final class NamespaceSorter
{
    /**
     * @var string
     */
    private const NAMESPACE_SEPARATOR = '\\';

    /**
     * @param mixed[] $namespaces
     * @return mixed[]
     */
    public function sortNamespaces(array $namespaces): array
    {
        if ($this->isNoneOnly($namespaces)) {
            return $namespaces;
        }

        $sortedNamespaces = [];
        $namespaceNames = array_keys($namespaces);

        foreach ($namespaceNames as $namespaceName) {
            $sortedNamespaces = $this->addMissingParentNamespaces($sortedNamespaces, $namespaceName);
            $sortedNamespaces = $this->addMissingElementTypes($sortedNamespaces, $namespaceName);
        }

        uksort($sortedNamespaces, function ($one, $two) {
            return $this->compareNamespaceNames($one, $two);
        });

        return $sortedNamespaces;
    }

    /**
     * @param mixed[] $namespaces
     */
    private function isNoneOnly(array $namespaces): bool
    {
        return count($namespaces) === 1 && isset($namespaces['None']);
    }

    /**
     * @param mixed[] $namespaces
     * @param string $namespaceName
     * @return mixed[]
     */
    private function addMissingParentNamespaces(array $namespaces, string $namespaceName): array
    {
        $parent = '';
        foreach (explode(self::NAMESPACE_SEPARATOR, $namespaceName) as $part) {
            $parent = ltrim($parent . self::NAMESPACE_SEPARATOR . $part, self::NAMESPACE_SEPARATOR);

            if (! isset($namespaces[$parent])) {
                $namespaces[$parent] = ElementEmptyListFactory::createBasicEmptyList();
            }
        }

        return $namespaces;
    }

//    /**
//     * @param mixed[] $namespaces
//     * @return mixed[]
//     */
//    private function addMissingElementTypes(array $namespaces, string $namespaceName): array
//    {
//        foreach (ElementEmptyListFactory::createBasicEmptyList() as $type) {
//            if (! isset($namespaces[$namespaceName][$type])) {
//                $namespaces[$namespaceName][$type] = [];
//            }
//        }
//
//        return $namespaces;
//    }

//    private function compareNamespaceNames(string $firstNamespace, string $secondNamespace): int
//    {
//        // \ as separator has to be first
//        $firstNamespace = str_replace(self::NAMESPACE_SEPARATOR, ' ', $firstNamespace);
//        $secondNamespace = str_replace(self::NAMESPACE_SEPARATOR, ' ', $secondNamespace);
//
//        return strcasecmp($firstNamespace, $secondNamespace);
//    }
}
