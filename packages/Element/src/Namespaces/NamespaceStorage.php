<?php declare(strict_types=1);

namespace ApiGen\Element\Namespaces;

use ApiGen\Reflection\Contract\ReflectionStorageInterface;
use Nette\Utils\Strings;

final class NamespaceStorage
{
    /**
     * @var string
     */
    private const NO_NAMESPACE = 'None';

    /**
     * @var string
     */
    private const NAMESPACE_SEPARATOR = '\\';

    /**
     * @var ReflectionStorageInterface
     */
    private $reflectionStorage;

    /**
     * @var SingleNamespaceStorage[]
     */
    private $singleNamespaceStorages = [];

    /**
     * @var mixed[]
     */
    private $reflectionsCategorizedToNamespaces = [];

    public function __construct(ReflectionStorageInterface $reflectionStorage)
    {
        $this->reflectionStorage = $reflectionStorage;
    }

    /**
     * @return string[]
     */
    public function getNamespaces(): array
    {
        $this->categorizeReflectionsToNamespaces();

        $namespaceNames = array_keys($this->reflectionsCategorizedToNamespaces);

        return $this->makeNoNamespaceNameLast($namespaceNames);
    }

    public function findInNamespace(string $namespaceToSeek): SingleNamespaceStorage
    {
        $this->categorizeReflectionsToNamespaces();

        $classes = [];
        $interfaces = [];
        $traits = [];
        $functions = [];

        foreach ($this->singleNamespaceStorages as $namespace => $singleNamespaceStorage) {
            if (! Strings::startsWith($namespace, $namespaceToSeek)) {
                continue;
            }

            $classes += $singleNamespaceStorage->getClassReflections();
            $interfaces += $singleNamespaceStorage->getInterfaceReflections();
            $traits += $singleNamespaceStorage->getTraitReflections();
            $functions += $singleNamespaceStorage->getFunctionReflections();
        }

        return new SingleNamespaceStorage(
            $namespaceToSeek,
            $this->getParentNamespaces($namespaceToSeek),
            array_unique($classes),
            array_unique($interfaces),
            array_unique($traits),
            array_unique($functions)
        );
    }

    private function categorizeReflectionsToNamespaces(): void
    {
        if ($this->reflectionsCategorizedToNamespaces !== []) {
            return;
        }

        $this->categorizeReflectionsToNamespace($this->reflectionStorage->getClassReflections(), 'classes');
        $this->categorizeReflectionsToNamespace($this->reflectionStorage->getInterfaceReflections(), 'interfaces');
        $this->categorizeReflectionsToNamespace($this->reflectionStorage->getTraitReflections(), 'traits');
        $this->categorizeReflectionsToNamespace($this->reflectionStorage->getFunctionReflections(), 'functions');

        // use value objects for API over array access
        $namespaceNames = array_keys($this->reflectionsCategorizedToNamespaces);
        foreach ($namespaceNames as $namespaceName) {
            $singleNamespaceElements = $this->reflectionsCategorizedToNamespaces[$namespaceName];
            $this->singleNamespaceStorages[$namespaceName] = new SingleNamespaceStorage(
                $namespaceName,
                $this->getParentNamespaces($namespaceName),
                $singleNamespaceElements['classes'] ?? [],
                $singleNamespaceElements['interfaces'] ?? [],
                $singleNamespaceElements['traits'] ?? [],
                $singleNamespaceElements['functions'] ?? []
            );
        }

        $this->makeNoNamespaceLast();
    }

    private function categorizeReflectionsToNamespace(array $reflections, string $type): void
    {
        foreach ($reflections as $reflection) {
            $namespace = $reflection->getNamespaceName() ?: self::NO_NAMESPACE;
            $this->reflectionsCategorizedToNamespaces[$namespace][$type][$reflection->getShortName()] = $reflection;
        }
    }

    /**
     * @return string[]
     */
    private function getParentNamespaces(string $namespace): array
    {
        $parentNamespaces = [];
        $parentNamespace = '';
        foreach (explode(self::NAMESPACE_SEPARATOR, $namespace) as $part) {
            $parentNamespace = ltrim($parentNamespace . self::NAMESPACE_SEPARATOR . $part, self::NAMESPACE_SEPARATOR);
            if ($parentNamespace === $namespace) {
                break;
            }
            $parentNamespaces[] = $parentNamespace;
        }

        sort($parentNamespaces);

        return $parentNamespaces;
    }

    private function makeNoNamespaceLast()
    {
        if (! isset($this->singleNamespaceStorages[self::NO_NAMESPACE])) {
            return;
        }

        $noNamespace = $this->singleNamespaceStorages[self::NO_NAMESPACE];
        unset($this->singleNamespaceStorages[self::NO_NAMESPACE]);
        $this->singleNamespaceStorages[self::NO_NAMESPACE] = $noNamespace;
    }

    /**
     * @param string[] $namespaceNames
     * @return string[]
     */
    private function makeNoNamespaceNameLast(array $namespaceNames): array
    {
        if (in_array(self::NO_NAMESPACE, $namespaceNames)) {
            $noNamespaceKey = array_search(self::NO_NAMESPACE, $namespaceNames);
            unset($namespaceNames[$noNamespaceKey]);
            $namespaceNames[] = self::NO_NAMESPACE;
        }

        return array_values($namespaceNames);
    }
}
