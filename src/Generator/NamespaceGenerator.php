<?php declare(strict_types=1);

namespace ApiGen\Generator;

use ApiGen\Configuration\Configuration;
use ApiGen\Contract\Generator\GeneratorInterface;
use ApiGen\Element\Namespaces\NamespaceStorage;
use ApiGen\Element\Namespaces\SingleNamespaceStorage;
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
     * @var NamespaceStorage
     */
    private $namespaceStorage;

    public function __construct(
        NamespaceStorage $namespaceStorage,
        Configuration $configuration,
        TemplateRenderer $templateRenderer
    ) {
        $this->namespaceStorage = $namespaceStorage;
        $this->configuration = $configuration;
        $this->templateRenderer = $templateRenderer;
    }

    public function generate(): void
    {
        foreach ($this->namespaceStorage->getNamespaces() as $namespace) {
            $singleNamespaceStorage = $this->namespaceStorage->findInNamespace($namespace);
            $this->generateForNamespace($singleNamespaceStorage);
        }
    }

    /**
     * @param mixed[] $elementsInNamespace
     */
    private function generateForNamespace(SingleNamespaceStorage $singleNamespaceStorage): void
    {
        $this->templateRenderer->renderToFile(
            $this->configuration->getTemplateByName('namespace'),
            $this->configuration->getDestinationWithPrefixName(
                'namespace-', $singleNamespaceStorage->getNamespace()
            ),
            [
                'activeNamespace' => $singleNamespaceStorage->getNamespace(),
                'childNamespaces' => $this->resolveChildNamespaces($singleNamespaceStorage->getNamespace()),
                'classes' => $singleNamespaceStorage->getClassReflections(),
                'interfaces' => $singleNamespaceStorage->getInterfaceReflections(),
                'traits' => $singleNamespaceStorage->getTraitReflections(),
                'functions' => $singleNamespaceStorage->getFunctionReflections()
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
        $namespaces = array();

        foreach ($this->namespaceStorage->getNamespaces() as $sub) {
            if (substr($sub, 0, $len) === $prefix
                && strpos(substr($sub, $len), '\\') === false
            ) {
                $namespaces[] = $sub;
            }
        }

        return $namespaces;
    }
}
