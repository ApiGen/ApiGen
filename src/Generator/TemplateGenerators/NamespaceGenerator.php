<?php declare(strict_types=1);

namespace ApiGen\Generator\TemplateGenerators;

use ApiGen\Contracts\Configuration\ConfigurationInterface;
use ApiGen\Contracts\Generator\NamedDestinationGeneratorInterface;
use ApiGen\Contracts\Parser\Elements\ElementStorageInterface;
use ApiGen\Contracts\Templating\TemplateFactory\TemplateFactoryInterface;
use ApiGen\Parser\Elements\Elements;

final class NamespaceGenerator implements NamedDestinationGeneratorInterface
{
    /**
     * @var TemplateFactoryInterface
     */
    private $templateFactory;

    /**
     * @var ElementStorageInterface
     */
    private $elementStorage;

    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    public function __construct(
        TemplateFactoryInterface $templateFactory,
        ElementStorageInterface $elementStorage,
        ConfigurationInterface $configuration
    ) {
        $this->templateFactory = $templateFactory;
        $this->elementStorage = $elementStorage;
        $this->configuration = $configuration;
    }

    public function generate(): void
    {
        foreach ($this->elementStorage->getNamespaces() as $namespace => $elementsInNamespace) {
            $this->generateForNamespace($namespace, $elementsInNamespace);
        }
    }

    public function getDestinationPath(string $namespace): string
    {
        return $this->configuration->getDestinationWithPrefixName('namespace-', $namespace);
    }

    /**
     * @param string $namespace
     * @param mixed[] $elementsInNamespace
     */
    private function generateForNamespace(string $namespace, array $elementsInNamespace): void
    {
        $template = $this->templateFactory->create();

        $template->setFile($this->getTemplateFile());

        $template->save($this->getDestinationPath($namespace), [
            'namespace' => $namespace,
            'subnamespaces' => $this->getSubnamesForName($namespace, $template->getParameters()['namespaces']),
            'classes' => $elementsInNamespace[Elements::CLASSES],
            'interfaces' => $elementsInNamespace[Elements::INTERFACES],
            'traits' => $elementsInNamespace[Elements::TRAITS],
            'exceptions' => $elementsInNamespace[Elements::EXCEPTIONS],
            'functions' => $elementsInNamespace[Elements::FUNCTIONS]
        ]);
    }

    /**
     * @param string $name
     * @param mixed[] $elements
     * @return string[]
     */
    private function getSubnamesForName(string $name, array $elements): array
    {
        return array_filter($elements, function ($subname) use ($name) {
            $pattern = '~^' . preg_quote($name) . '\\\\[^\\\\]+$~';
            return (bool) preg_match($pattern, $subname);
        });
    }

    private function getTemplateFile(): string
    {
        return $this->configuration->getTemplateByName('namespace');
    }
}
