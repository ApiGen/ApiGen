<?php declare(strict_types=1);

namespace ApiGen\Generator;

use ApiGen\Contracts\Configuration\ConfigurationInterface;
use ApiGen\Contracts\Generator\GeneratorInterface;
use ApiGen\Contracts\Templating\TemplateRendererInterface;
use ApiGen\Element\Namespaces\NamespaceStorage;
use ApiGen\Element\Namespaces\SingleNamespaceStorage;

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
                'namespace' => $singleNamespaceStorage->getNamespace(),
                'subnamespaces' => $singleNamespaceStorage->getSubNamespaces(),
                'classes' => $singleNamespaceStorage->getClassReflections(),
                'interfaces' => $singleNamespaceStorage->getInterfaceReflections(),
                'traits' => $singleNamespaceStorage->getTraitReflections(),
                'functions' => $singleNamespaceStorage->getFunctionReflections()
            ]
        );
    }
}
