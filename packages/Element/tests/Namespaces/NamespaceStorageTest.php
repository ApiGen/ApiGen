<?php declare(strict_types=1);

namespace ApiGen\Element\Tests\Namespaces;

use ApiGen\Element\Namespaces\NamespaceStorage;
use ApiGen\Element\ReflectionCollector\NamespaceReflectionCollector;
use ApiGen\Reflection\Contract\ParserInterface;
use ApiGen\Tests\AbstractContainerAwareTestCase;
use PhpParser\NodeVisitor\NameResolver;

final class NamespaceStorageTest extends AbstractContainerAwareTestCase
{
    /**
     * @var string
     */
    private $namespacePrefix = 'ApiGen\Element\Tests\Namespaces\Source';

    /**
     * @var NamespaceStorage
     */
    private $namespaceStorage;

    /**
     * @var NamespaceReflectionCollector
     */
    private $namespaceReflectionCollector;

    protected function setUp(): void
    {
        /** @var ParserInterface $parser */
        $parser = $this->container->getByType(ParserInterface::class);
        $parser->parseDirectories([__DIR__ . '/Source']);

        $this->namespaceStorage = $this->container->getByType(NamespaceStorage::class);
        $this->namespaceReflectionCollector = $this->container->getByType(NamespaceReflectionCollector::class);
    }

    public function testSort(): void
    {
        $collectedNamespaces = $this->namespaceReflectionCollector->getNamespaces();
        $this->assertCount(3, $collectedNamespaces);

        $namespaces = $this->namespaceStorage->getNamespaces();
        $this->assertCount(3, $namespaces);

        $this->assertSame([
            $this->namespacePrefix,
            $this->namespacePrefix . '\SubNamespace',
            NamespaceStorage::NO_NAMESPACE,
        ], $namespaces);

        $this->assertSame($collectedNamespaces, $namespaces);
    }

    public function testFindInNamespace(): void
    {
        $namespacedItems = $this->namespaceStorage->findInNamespace($this->namespacePrefix);

        $this->assertCount(1, $namespacedItems->getClassReflections());
        $this->assertCount(1, $namespacedItems->getTraitReflections());
        $this->assertCount(1, $namespacedItems->getFunctionReflections());
        $this->assertCount(1, $namespacedItems->getInterfaceReflections());

        $namespacedItems = $this->namespaceStorage->findInNamespace($this->namespacePrefix . '\SubNamespace');
        $this->assertCount(0, $namespacedItems->getClassReflections());
        $this->assertCount(1, $namespacedItems->getTraitReflections());
        $this->assertCount(0, $namespacedItems->getFunctionReflections());
        $this->assertCount(1, $namespacedItems->getInterfaceReflections());

        $this->namespaceReflectionCollector->setActiveNamespace($this->namespacePrefix);
        $this->assertCount(1, $this->namespaceReflectionCollector->getClassReflections());
        $this->assertCount(0, $this->namespaceReflectionCollector->getTraitReflections());
        $this->assertCount(1, $this->namespaceReflectionCollector->getFunctionReflections());
        $this->assertCount(0, $this->namespaceReflectionCollector->getInterfaceReflections());

        $this->namespaceReflectionCollector->setActiveNamespace($this->namespacePrefix . '\SubNamespace');
        $this->assertCount(0, $this->namespaceReflectionCollector->getClassReflections());
        $this->assertCount(1, $this->namespaceReflectionCollector->getTraitReflections());
        $this->assertCount(0, $this->namespaceReflectionCollector->getFunctionReflections());
        $this->assertCount(1, $this->namespaceReflectionCollector->getInterfaceReflections());
    }

    public function testNoneNamespace(): void
    {
        $namespacedItems = $this->namespaceStorage->findInNamespace('None');

        $this->assertCount(1, $namespacedItems->getClassReflections());

        $this->namespaceReflectionCollector->setActiveNamespace('None');

        $this->assertCount(1, $this->namespaceReflectionCollector->getClassReflections());
    }
}
