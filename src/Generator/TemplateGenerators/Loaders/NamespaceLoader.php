<?php declare(strict_types=1);

namespace ApiGen\Generator\TemplateGenerators\Loaders;

use ApiGen\Contracts\Parser\Reflection\ElementReflectionInterface;
use ApiGen\Parser\Elements\Elements;
use ApiGen\Parser\Elements\ElementStorage;
use ApiGen\Templating\Template;

class NamespaceLoader
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

        return $this->loadTemplateWithNamespace($template, $name, $namespaces[$name]);
    }


    /**
     * @param Template $template
     * @param string $name
     * @param array $namespace
     * @return Template
     */
    public function loadTemplateWithNamespace(Template $template, string $name, array $namespace): Template
    {
        $template->setParameters([
            'package' => null,
            'namespace' => $name,
            'subnamespaces' => $this->getSubnamesForName($name, $template->getParameters()['namespaces'])
        ]);
        $template = $this->loadTemplateWithElements($template, $namespace);
        return $template;
    }


    /**
     * @param Template $template
     * @param array $elements
     * @return Template
     */
    private function loadTemplateWithElements(Template $template, array $elements): Template
    {
        return $template->setParameters([
            Elements::CLASSES => $elements[Elements::CLASSES],
            Elements::INTERFACES => $elements[Elements::INTERFACES],
            Elements::TRAITS => $elements[Elements::TRAITS],
            Elements::EXCEPTIONS => $elements[Elements::EXCEPTIONS],
            Elements::CONSTANTS => $elements[Elements::CONSTANTS],
            Elements::FUNCTIONS => $elements[Elements::FUNCTIONS]
        ]);
    }


    /**
     * @param string $name
     * @param array $elements
     */
    private function getSubnamesForName(string $name, $elements): array
    {
        return array_filter($elements, function ($subname) use ($name) {
            $pattern = '~^' . preg_quote($name) . '\\\\[^\\\\]+$~';
            return (bool) preg_match($pattern, $subname);
        });
    }
}
