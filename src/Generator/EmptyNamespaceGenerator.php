<?php declare(strict_types=1);

namespace ApiGen\Generator;

use ApiGen\Configuration\Configuration;
use ApiGen\Contract\Generator\GeneratorInterface;
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

    public function __construct(
        NamespaceReflectionCollector $namespaceReflectionCollector,
        Configuration $configuration,
        TemplateRenderer $templateRenderer,
        ParentEmptyNamespacesResolver $parentEmptyNamespacesResolver
    ) {
        $this->namespaceReflectionCollector = $namespaceReflectionCollector;
        $this->configuration = $configuration;
        $this->templateRenderer = $templateRenderer;
        $this->parentEmptyNamespacesResolver = $parentEmptyNamespacesResolver;
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
            $this->configuration->getDestinationWithPrefixName(
                'namespace-', $namespace
            ),
            [
                'activePage' => 'namespace',
                'activeNamespace' => $namespace,
                'childNamespaces' => $this->resolveChildNamespaces($namespace),
                'classes' => [],
                'interfaces' => [],
                'traits' => [],
                'functions' => [],
            ]
        );
    }

    /**
     * @todo: move to service!
     * @return string[]
     */
    private function resolveChildNamespaces(string $namespace): array
    {
        $prefix = $namespace . '\\';
        $len = strlen($prefix);
        $namespaces = array();

        foreach ($this->namespaceReflectionCollector->getNamespaces() as $sub) {
            if (substr($sub, 0, $len) === $prefix
                && strpos(substr($sub, $len), '\\') === false
            ) {
                $namespaces[] = $sub;
            }
        }

        return $namespaces;
    }
}
