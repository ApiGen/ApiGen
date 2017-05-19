<?php declare(strict_types=1);

namespace ApiGen\Tests\Templating\Filters;

use ApiGen\Reflection\Contract\Reflection\Class_\ClassReflectionInterface;
use ApiGen\Tests\AbstractContainerAwareTestCase;

final class NamespaceUrlFiltersTest extends AbstractContainerAwareTestCase
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

    public function testsubNamespaceName(): void
    {
        $this->assertSame('Subgroup', $this->namespaceUrlFilters->subNamespaceName('Group\\Subgroup'));
        $this->assertSame('Group', $this->namespaceUrlFilters->subNamespaceName('Group'));
    }
}
