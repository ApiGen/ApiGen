<?php declare(strict_types=1);

namespace ApiGen\Generator\TemplateGenerators\Loaders;

use ApiGen\Contracts\Parser\Elements\ElementStorageInterface;
use ApiGen\Contracts\Parser\Reflection\ElementReflectionInterface;
use ApiGen\Parser\Elements\Elements;
use ApiGen\Templating\Template;

final class NamespaceLoader
{
    /**
     * @var ElementStorageInterface
     */
    private $elementStorage;

    public function __construct(ElementStorageInterface $elementStorage)
    {
        $this->elementStorage = $elementStorage;
    }

    public function loadTemplateWithElementNamespace(Template $template, ElementReflectionInterface $element): Template
    {
        $name = $element->getPseudoNamespaceName();
        $namespaces = $this->elementStorage->getNamespaces();

        $this->loadTemplateWithNamespace($template, $name, $namespaces[$name]);

        return $template;
    }

    /**
     * @param Template $template
     * @param string $name
     * @param mixed[] $namespace
     */
    public function loadTemplateWithNamespace(
        Template $template,
        string $name,
        array $namespace
    ): void {
        $template->setParameters([
            'namespace' => $name,
            'subnamespaces' => $this->getSubnamesForName($name, $template->getParameters()['namespaces'])
        ]);
        $this->loadTemplateWithElements($template, $namespace);
    }

    /**
     * @param Template $template
     * @param mixed[] $elements
     */
    private function loadTemplateWithElements(Template $template, array $elements): void
    {
        $template->setParameters([
            Elements::CLASSES => $elements[Elements::CLASSES],
            Elements::INTERFACES => $elements[Elements::INTERFACES],
            Elements::TRAITS => $elements[Elements::TRAITS],
            Elements::EXCEPTIONS => $elements[Elements::EXCEPTIONS],
            Elements::FUNCTIONS => $elements[Elements::FUNCTIONS]
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
}
