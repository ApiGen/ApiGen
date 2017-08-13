<?php declare(strict_types=1);

namespace ApiGen\Element\Tests\Namespace_;

use ApiGen\Element\Namespace_\ParentEmptyNamespacesResolver;
use ApiGen\Tests\AbstractContainerAwareTestCase;

final class ParentEmptyNamespacesResolverTest extends AbstractContainerAwareTestCase
{
    /**
     * @var ParentEmptyNamespacesResolver
     */
    private $parentEmptyNamespacesResolver;

    protected function setUp(): void
    {
        $this->parentEmptyNamespacesResolver = $this->container->get(ParentEmptyNamespacesResolver::class);
    }

    /**
     * @dataProvider provideNamespaceData()
     * @param string[] $resolvedNamespaces
     * @param string[] $namespaces
     */
    public function test(array $resolvedNamespaces, array $namespaces): void
    {
        $this->assertSame(
            $resolvedNamespaces,
            $this->parentEmptyNamespacesResolver->resolve($namespaces)
        );
    }

    /**
     * @return mixed[]
     */
    public function provideNamespaceData(): array
    {
        return [
            [['Parent', 'Parent\Namespace'], ['Parent\Namespace\SubNamespace']],
            [['Parent'], ['Parent\Namespace\SubNamespace', 'Parent\Namespace']],
            [[], ['Parent', 'Parent\Namespace\SubNamespace', 'Parent\Namespace']],
        ];
    }
}
