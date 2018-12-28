<?php declare(strict_types=1);

namespace ApiGen\Generator;

use ApiGen\Configuration\Configuration;
use ApiGen\Contract\Generator\GeneratorInterface;
use ApiGen\Element\ReflectionCollector\NamespaceReflectionCollector;
use ApiGen\Templating\TemplateRenderer;

final class NamespaceGenerator implements GeneratorInterface
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

    public function __construct(
        NamespaceReflectionCollector $namespaceReflectionCollector,
        Configuration $configuration,
        TemplateRenderer $templateRenderer
    ) {
        $this->namespaceReflectionCollector = $namespaceReflectionCollector;
        $this->configuration = $configuration;
        $this->templateRenderer = $templateRenderer;
    }

    public function generate(): void
    {
        foreach ($this->namespaceReflectionCollector->getNamespaces() as $namespace) {
            $this->generateForNamespace($namespace, $this->namespaceReflectionCollector);
        }
    }

    private function generateForNamespace(
        string $namespace,
        NamespaceReflectionCollector $namespaceReflectionCollector
    ): void {
        $this->templateRenderer->renderToFile(
            $this->configuration->getTemplateByName('namespace'),
            $this->configuration->getDestinationWithPrefixName('namespace-', $namespace),
            [
                'activePage' => 'namespace',
                'activeNamespace' => $namespace,
                'childNamespaces' => $this->resolveChildNamespaces($namespace),
                'classes' => $namespaceReflectionCollector->getClassReflections($namespace),
                'exceptions' => $namespaceReflectionCollector->getExceptionReflections($namespace),
                'interfaces' => $namespaceReflectionCollector->getInterfaceReflections($namespace),
                'traits' => $namespaceReflectionCollector->getTraitReflections($namespace),
                'functions' => $namespaceReflectionCollector->getFunctionReflections($namespace),
            ]
        );
    }

    /**
     * @return string[]
     */
    private function resolveChildNamespaces(string $namespace): array
    {
        $prefix = $namespace . '\\';
        $len = strlen($prefix);
        $namespaces = [];

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
