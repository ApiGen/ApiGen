<?php declare(strict_types=1);

namespace ApiGen\Generator\TemplateGenerators\Loaders;

use ApiGen\Contracts\Parser\Reflection\ElementReflectionInterface;
use ApiGen\Parser\Elements\Elements;
use ApiGen\Parser\Elements\ElementStorage;
use ApiGen\Templating\Template;

final class NamespaceLoader
{

    /**
     * @var ElementStorage
     */
    private $elementStorage;


    public function __construct(ElementStorage $elementStorage)
    {
        $this->elementStorage = $elementStorage;
    }


    public function loadTemplateWithElementNamespace(Template $template, ElementReflectionInterface $element): Template
    {
        $namespaces = $this->elementStorage->getNamespaces();
        $name = $element->getPseudoNamespaceName();

        $this->loadTemplateWithNamespace($template, $name, $namespaces[$name]);

        return $template;
    }


    public function loadTemplateWithNamespace(Template $template, string $name, array $namespace): void
    {
        $template->setParameters([
            'package' => null, // removed, but for BC in Themes
            'namespace' => $name,
            'subnamespaces' => $this->getSubnamesForName($name, $template->getParameters()['namespaces'])
        ]);
        $this->loadTemplateWithElements($template, $namespace);
    }


    private function loadTemplateWithElements(Template $template, array $elements): void
    {
        $template->setParameters([
            Elements::CLASSES => $elements[Elements::CLASSES],
            Elements::INTERFACES => $elements[Elements::INTERFACES],
            Elements::TRAITS => $elements[Elements::TRAITS],
            Elements::EXCEPTIONS => $elements[Elements::EXCEPTIONS],
            Elements::CONSTANTS => $elements[Elements::CONSTANTS],
            Elements::FUNCTIONS => $elements[Elements::FUNCTIONS]
        ]);
    }


    private function getSubnamesForName(string $name, array $elements): array
    {
        return array_filter($elements, function ($subname) use ($name) {
            $pattern = '~^' . preg_quote($name) . '\\\\[^\\\\]+$~';
            return (bool) preg_match($pattern, $subname);
        });
    }
}
