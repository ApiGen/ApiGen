<?php declare(strict_types=1);

namespace ApiGen\Element\Tests\Namespaces;

use ApiGen\Element\Namespaces\NamespaceStorage;
use ApiGen\Reflection\Contract\ParserInterface;
use ApiGen\Tests\AbstractContainerAwareTestCase;

final class NamespaceStorageTest extends AbstractContainerAwareTestCase
{
    /**
     * @var string
     */
    private $namespacePrefix = 'ApiGen\Element\Tests\Namespaces';

    /**
     * @var NamespaceStorage
     */
    private $namespaceStorage;

    protected function setUp(): void
    {
        /** @var ParserInterface $parser */
        $parser = $this->container->getByType(ParserInterface::class);
        $parser->parseDirectories([__DIR__ . '/../Source']);

        $this->namespaceStorage = $this->container->getByType(NamespaceStorage::class);
    }

    public function testSort(): void
    {
        $namespaces = $this->namespaceStorage->getNamespaces();
        $this->assertCount(3, $namespaces);

        $this->assertSame([
            $this->namespacePrefix,
            $this->namespacePrefix . '\SubNamespace',
            'None',
        ], $namespaces);
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
    }

    public function testNoneNamespace(): void
    {
        $namespacedItems = $this->namespaceStorage->findInNamespace('None');

        $this->assertCount(1, $namespacedItems->getClassReflections());
    }

    public function testNamespace(): void
    {
        $namespacedItems = $this->namespaceStorage->findInNamespace($this->namespacePrefix);

        $this->assertSame($this->namespacePrefix, $namespacedItems->getNamespace());
        $this->assertSame([
            'ApiGen',
            'ApiGen\Element',
            'ApiGen\Element\Tests'
        ], $namespacedItems->getParentNamespaces());
    }
}
