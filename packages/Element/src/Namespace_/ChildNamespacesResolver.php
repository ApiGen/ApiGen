<?php declare(strict_types=1);

namespace ApiGen\Element\Namespace_;

use ApiGen\Element\ReflectionCollector\NamespaceReflectionCollector;

final class ChildNamespacesResolver
{
    /**
     * @var NamespaceReflectionCollector
     */
    private $namespaceReflectionCollector;

    public function __construct(NamespaceReflectionCollector $namespaceReflectionCollector)
    {
        $this->namespaceReflectionCollector = $namespaceReflectionCollector;
    }

    /**
     * @return string[]
     */
    public function resolve(string $namespace): array
    {
        $prefix = $namespace . '\\';
        $len = strlen($prefix);
        $namespaces = array();

        foreach ($this->namespaceReflectionCollector->getNamespaces() as $sub) {
            if (substr($sub, 0, $len) === $prefix
                && strpos(substr($sub, $len), '\\') === false
            ) {
                $namespaces[] = $sub;
            }
        }

        return $namespaces;
    }
}
