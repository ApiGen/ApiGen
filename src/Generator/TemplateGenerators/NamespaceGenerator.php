<?php declare(strict_types=1);

namespace ApiGen\Generator\TemplateGenerators;

use ApiGen\Contracts\Configuration\ConfigurationInterface;
use ApiGen\Contracts\Generator\GeneratorInterface;
use ApiGen\Contracts\Parser\Elements\ElementStorageInterface;
use ApiGen\Contracts\Templating\TemplateRendererInterface;
use ApiGen\Parser\Elements\Elements;

final class NamespaceGenerator implements GeneratorInterface
{
    /**
     * @var ElementStorageInterface
     */
    private $elementStorage;

    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     * @var TemplateRendererInterface
     */
    private $templateRenderer;

    public function __construct(
        ElementStorageInterface $elementStorage,
        ConfigurationInterface $configuration,
        TemplateRendererInterface $templateRenderer
    ) {
        $this->elementStorage = $elementStorage;
        $this->configuration = $configuration;
        $this->templateRenderer = $templateRenderer;
    }

    public function generate(): void
    {
        foreach ($this->elementStorage->getNamespaces() as $namespace => $elementsInNamespace) {
            $this->generateForNamespace($namespace, $elementsInNamespace);
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
                'classes' => $elementsInNamespace[Elements::CLASSES],
                'interfaces' => $elementsInNamespace[Elements::INTERFACES],
                'traits' => $elementsInNamespace[Elements::TRAITS],
                'exceptions' => $elementsInNamespace[Elements::EXCEPTIONS],
                'functions' => $elementsInNamespace[Elements::FUNCTIONS]
            ]
        );
    }

    /**
     * @return string[]
     */
    private function getSubnamesForName(string $name): array
    {
        $allNamespaces = array_keys($this->elementStorage->getNamespaces());

        return array_filter($allNamespaces, function ($subname) use ($name) {
            $pattern = '~^' . preg_quote($name) . '\\\\[^\\\\]+$~';
            return (bool) preg_match($pattern, $subname);
        });
    }
}
