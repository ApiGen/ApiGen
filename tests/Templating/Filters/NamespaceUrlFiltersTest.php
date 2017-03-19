<?php declare(strict_types=1);

namespace ApiGen\Tests\Templating\Filters;

use ApiGen\Contracts\Parser\ParserStorageInterface;
use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Templating\Filters\NamespaceUrlFilters;
use ApiGen\Tests\ContainerAwareTestCase;

final class NamespaceUrlFiltersTest extends ContainerAwareTestCase
{
    /**
     * @var NamespaceUrlFilters
     */
    private $namespaceUrlFilters;


    protected function setUp(): void
    {
        $this->namespaceUrlFilters = $this->container->getByType(NamespaceUrlFilters::class);

        $classReflectionMock = $this->createMock(ClassReflectionInterface::class);
        $classReflectionMock->method('isDocumented')
            ->willReturn(true);

        /** @var ParserStorageInterface $parserStorage */
        $parserStorage = $this->container->getByType(ParserStorageInterface::class);
        $parserStorage->setClasses([
            'Long\Namespace\SomeClass' => $classReflectionMock
        ]);
    }


    public function testNamespaceUrl(): void
    {
        $this->assertSame(
            'namespace-Long.Namespace.html',
            $this->namespaceUrlFilters->namespaceUrl('Long\\Namespace')
        );
    }


    public function testNamespaceLinks(): void
    {
        $this->assertSame(
            '<a href="namespace-Long.html">Long</a>\<a href="namespace-Long.Namespace.html">Namespace</a>',
            $this->namespaceUrlFilters->namespaceLinks('Long\Namespace')
        );
    }

    public function testNamespaceLinksWithNoNamespaces(): void
    {
        $this->assertSame(
            '<a href="namespace-Long.html">Long</a>\<a href="namespace-Long.Namespace.html">Namespace</a>',
            $this->namespaceUrlFilters->namespaceLinks('Long\\Namespace')
        );
    }


    public function testSubgroupName(): void
    {
        $this->assertSame('Subgroup', $this->namespaceUrlFilters->subgroupName('Group\\Subgroup'));
        $this->assertSame('Group', $this->namespaceUrlFilters->subgroupName('Group'));
    }
}
