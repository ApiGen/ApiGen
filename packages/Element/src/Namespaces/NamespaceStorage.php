<?php declare(strict_types=1);

namespace ApiGen\Element\Namespaces;

use ApiGen\Reflection\Contract\ReflectionStorageInterface;
use Nette\Utils\Strings;

final class NamespaceStorage
{
    /**
     * @var string
     */
    public const NO_NAMESPACE = 'None';

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

        sort($namespaceNames);

        return $namespaceNames;
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
            $classes,
            $interfaces,
            $traits,
            $functions
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
                $singleNamespaceElements['classes'] ?? [],
                $singleNamespaceElements['interfaces'] ?? [],
                $singleNamespaceElements['traits'] ?? [],
                $singleNamespaceElements['functions'] ?? []
            );
        }
    }

    /**
     * @param mixed[] $reflections
     */
    private function categorizeReflectionsToNamespace(array $reflections, string $type): void
    {
        foreach ($reflections as $reflection) {
            $namespace = $reflection->getNamespaceName() ?: self::NO_NAMESPACE;
            $this->reflectionsCategorizedToNamespaces[$namespace][$type][$reflection->getShortName()] = $reflection;
        }
    }
}
