<?php declare(strict_types=1);

namespace ApiGen\Generator;

use ApiGen\Contract\Configuration\ConfigurationInterface;
use ApiGen\Contract\Generator\GeneratorInterface;
use ApiGen\Element\Namespaces\NamespaceStorage;
use ApiGen\Element\Namespaces\SingleNamespaceStorage;
use ApiGen\Templating\TemplateRenderer;

final class NamespaceGenerator implements GeneratorInterface
{
    /**
     * @var ConfigurationInterface
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
        ConfigurationInterface $configuration,
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
                'parentNamespaces' => $singleNamespaceStorage->getParentNamespaces(),
                'classes' => $singleNamespaceStorage->getClassReflections(),
                'interfaces' => $singleNamespaceStorage->getInterfaceReflections(),
                'traits' => $singleNamespaceStorage->getTraitReflections(),
                'functions' => $singleNamespaceStorage->getFunctionReflections()
            ]
        );
    }
}
