<?php declare(strict_types=1);

namespace ApiGen\Element\Namespaces;

use ApiGen\Reflection\Contract\ReflectionStorageInterface;
use Nette\Utils\Strings;

final class NamespaceStorage
{
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
        $this->populateAllParentNamespaces();

        return array_keys($this->reflectionsCategorizedToNamespaces);
    }

    public function findInNamespace(string $namespaceToSeek): SingleNamespaceStorage
    {
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
        foreach ($this->getNamespaces() as $namespace) {
            $singleNamespaceElements = $this->reflectionsCategorizedToNamespaces[$namespace];
            $this->singleNamespaceStorages[$namespace] = new SingleNamespaceStorage(
                $namespace,
                $this->getParentNamespaces($namespace),
                $singleNamespaceElements['classes'] ?? [],
                $singleNamespaceElements['interfaces'] ?? [],
                $singleNamespaceElements['traits'] ?? [],
                $singleNamespaceElements['functions'] ?? []
            );
        }
    }

    private function categorizeReflectionsToNamespace(array $reflections, string $type): void
    {
        foreach ($reflections as $reflection) {
            $namespace = $reflection->getNamespace() ?: 'None';
            $this->reflectionsCategorizedToNamespaces[$namespace][$type][$reflection->getShortName()] = $reflection;
        }
    }

    private function populateAllParentNamespaces(): void
    {
        foreach (array_keys($this->reflectionsCategorizedToNamespaces) as $namespace) {
            $parentNamespaces = $this->getParentNamespaces($namespace);
            foreach ($parentNamespaces as $parentNamespace) {
                if (isset($this->reflectionsCategorizedToNamespaces[$parentNamespace])) {
                    continue;
                }

                $this->reflectionsCategorizedToNamespaces[$parentNamespace] = new SingleNamespaceStorage(
                    $parentNamespace, $this->getParentNamespaces($parentNamespace), [], [], [], []
                );
            }
        }
    }

    /**
     * @return string[]
     */
    private function getParentNamespaces(string $namespace): array
    {
        $parentNamespaces[] = [];
        $parentNamespace = '';
        foreach (explode(self::NAMESPACE_SEPARATOR, $namespace) as $part) {
            $parentNamespace = ltrim($parentNamespace . self::NAMESPACE_SEPARATOR . $part, self::NAMESPACE_SEPARATOR);
            $parentNamespaces[] = $parentNamespace;
        }

        asort($parentNamespaces);

        return $parentNamespaces;
    }
}
