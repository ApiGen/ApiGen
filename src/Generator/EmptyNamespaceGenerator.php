<?php declare(strict_types=1);

namespace ApiGen\Generator;

use ApiGen\Configuration\Configuration;
use ApiGen\Contract\Generator\GeneratorInterface;
use ApiGen\Element\Namespace_\ChildNamespacesResolver;
use ApiGen\Element\Namespace_\ParentEmptyNamespacesResolver;
use ApiGen\Element\ReflectionCollector\NamespaceReflectionCollector;
use ApiGen\Templating\TemplateRenderer;

final class EmptyNamespaceGenerator implements GeneratorInterface
{
    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var TemplateRenderer
     */
    private $templateRenderer;

    /**
     * @var NamespaceReflectionCollector
     */
    private $namespaceReflectionCollector;

    /**
     * @var ParentEmptyNamespacesResolver
     */
    private $parentEmptyNamespacesResolver;

    /**
     * @var ChildNamespacesResolver
     */
    private $childNamespacesResolver;

    public function __construct(
        NamespaceReflectionCollector $namespaceReflectionCollector,
        Configuration $configuration,
        TemplateRenderer $templateRenderer,
        ParentEmptyNamespacesResolver $parentEmptyNamespacesResolver,
        ChildNamespacesResolver $childNamespacesResolver
    ) {
        $this->namespaceReflectionCollector = $namespaceReflectionCollector;
        $this->configuration = $configuration;
        $this->templateRenderer = $templateRenderer;
        $this->parentEmptyNamespacesResolver = $parentEmptyNamespacesResolver;
        $this->childNamespacesResolver = $childNamespacesResolver;
    }

    public function generate(): void
    {
        $parentEmptyNamespaces = $this->parentEmptyNamespacesResolver->resolve(
            $this->namespaceReflectionCollector->getNamespaces()
        );

        foreach ($parentEmptyNamespaces as $namespace) {
            $this->generateForNamespace($namespace);
        }
    }

    private function generateForNamespace(string $namespace): void
    {
        $this->templateRenderer->renderToFile(
            $this->configuration->getTemplateByName('namespace'),
            $this->configuration->getDestinationWithPrefixName('namespace-', $namespace),
            [
                'activePage' => 'namespace',
                'activeNamespace' => $namespace,
                'childNamespaces' => $this->childNamespacesResolver->resolve($namespace),
                'classes' => [],
                'exceptions' => [],
                'interfaces' => [],
                'traits' => [],
                'functions' => [],
            ]
        );
    }
}
