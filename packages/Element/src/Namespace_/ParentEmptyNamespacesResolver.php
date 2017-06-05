<?php declare(strict_types=1);

namespace ApiGen\Element\Namespace_;

final class ParentEmptyNamespacesResolver
{
    /**
     * @var string
     */
    private const NAMESPACE_SEPARATOR = '\\';

    /**
     * @param string[] $namespaces
     * @return string[]
     */
    public function resolve(array $namespaces): array
    {
        $parentEmptyNamespaces = [];

        foreach ($namespaces as $namespace) {
            while ($namespace = substr($namespace, 0, (int) strrpos($namespace, self::NAMESPACE_SEPARATOR))) {
                $parentEmptyNamespaces[] = $namespace;
            }
        }

        $parentEmptyNamespaces = array_unique($parentEmptyNamespaces);
        $parentEmptyNamespaces = array_diff($parentEmptyNamespaces, $namespaces);
        sort($parentEmptyNamespaces);

        return $parentEmptyNamespaces;
    }
}
