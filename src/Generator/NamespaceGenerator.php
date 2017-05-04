<?php declare(strict_types=1);

namespace ApiGen\Generator;

use ApiGen\Contracts\Configuration\ConfigurationInterface;
use ApiGen\Contracts\Generator\GeneratorInterface;
use ApiGen\Contracts\Templating\TemplateRendererInterface;
use ApiGen\Namespaces\NamespaceStorage;

final class NamespaceGenerator implements GeneratorInterface
{
    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     * @var TemplateRendererInterface
     */
    private $templateRenderer;
    /**
     * @var NamespaceStorage
     */
    private $namespaceStorage;

    public function __construct(
        NamespaceStorage $namespaceStorage,
        ConfigurationInterface $configuration,
        TemplateRendererInterface $templateRenderer
    ) {
        $this->namespaceStorage = $namespaceStorage;
        $this->configuration = $configuration;
        $this->templateRenderer = $templateRenderer;
    }

    public function generate(): void
    {
        $reflectionsCategorizedToNamespaces = $this->namespaceStorage->getReflectionsCategorizedToNamespaces();
        foreach ($reflectionsCategorizedToNamespaces as $namespace => $reflectionsInNamespace) {
            $this->generateForNamespace($namespace, $reflectionsInNamespace);
        }
    }

    /**
     * @param mixed[] $elementsInNamespace
     */
    private function generateForNamespace(string $namespace, array $elementsInNamespace): void
    {
        $this->templateRenderer->renderToFile(
            $this->configuration->getTemplateByName('namespace'),
            $this->configuration->getDestinationWithPrefixName('namespace-', $namespace),
            [
                'namespace' => $namespace,
                'subnamespaces' => $this->getSubnamesForName($namespace),
                'classes' => $elementsInNamespace['classes'],
                'interfaces' => $elementsInNamespace['interfaces'],
                'traits' => $elementsInNamespace['traits'],
                'functions' => $elementsInNamespace['functions']
            ]
        );
    }

    /**
     * @return string[]
     */
    private function getSubnamesForName(string $name): array
    {
        $allNamespaces = $this->namespaceStorage->getNamespaces();

        return array_filter($allNamespaces, function ($subname) use ($name) {
            $pattern = '~^' . preg_quote($name) . '\\\\[^\\\\]+$~';
            return (bool) preg_match($pattern, $subname);
        });
    }
}
