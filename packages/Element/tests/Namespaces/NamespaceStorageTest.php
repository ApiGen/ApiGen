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
    private $namespacePrefix = 'ApiGen\Element\Tests\Namespaces\Source';

    /**
     * @var NamespaceStorage
     */
    private $namespaceStorage;

    protected function setUp()
    {
        /** @var ParserInterface $parser */
        $parser = $this->container->getByType(ParserInterface::class);
        $parser->parseDirectories([__DIR__ . '/Source']);

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

    public function testFindInNamespace()
    {
        $namespacedItems = $this->namespaceStorage->findInNamespace($this->namespacePrefix);
        $this->assertCount(1, $namespacedItems->getClassReflections());
        $this->assertCount(1, $namespacedItems->getTraitReflections());

        $namespacedItems = $this->namespaceStorage->findInNamespace($this->namespacePrefix . '\SubNamespace');
        $this->assertCount(0, $namespacedItems->getClassReflections());
        $this->assertCount(1, $namespacedItems->getTraitReflections());
    }

    public function testNoneNamespace(): void
    {
        $namespacedItems = $this->namespaceStorage->findInNamespace('None');

        $this->assertCount(1, $namespacedItems->getClassReflections());
    }

    public function testNamespace()
    {
        $namespacedItems = $this->namespaceStorage->findInNamespace($this->namespacePrefix);

        $this->assertSame($this->namespacePrefix, $namespacedItems->getNamespace());
        $this->assertSame([
            'ApiGen',
            'ApiGen\Element',
            'ApiGen\Element\Tests',
            'ApiGen\Element\Tests\Namespaces',
        ], $namespacedItems->getParentNamespaces());
    }


//    public function testAddMissingParentNamespaces(): void
//    {
//        $this->assertNull(Assert::getObjectAttribute($this->namespaceSorter, 'namespaces'));
//        MethodInvoker::callMethodOnObject(
//            $this->namespaceSorter, 'addMissingParentNamespaces', ['Some\Group\Name']
//        );
//
//        $groups = Assert::getObjectAttribute($this->namespaceSorter, 'namespaces');
//        $this->assertArrayHasKey('Some\Group\Name', $groups);
//        $this->assertArrayHasKey('Some\Group', $groups);
//        $this->assertArrayHasKey('Some', $groups);
//    }
//
//    public function testAddMissingElementTypes(): void
//    {
//        MethodInvoker::callMethodOnObject($this->namespaceSorter, 'addMissingElementTypes', ['Some\Group']);
//        $groups = Assert::getObjectAttribute($this->namespaceSorter, 'namespaces');
//        $this->assertArrayHasKey('Some\Group', $groups);
//
//        $someGroup = $groups['Some\Group'];
//        $this->assertArrayHasKey('classes', $someGroup);
//        $this->assertArrayHasKey('exceptions', $someGroup);
//        $this->assertArrayHasKey('functions', $someGroup);
//        $this->assertArrayHasKey('interfaces', $someGroup);
//        $this->assertArrayHasKey('traits', $someGroup);
//    }
}
