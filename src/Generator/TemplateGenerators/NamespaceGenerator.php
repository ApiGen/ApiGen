<?php declare(strict_types=1);

namespace ApiGen\Generator\TemplateGenerators;

use ApiGen\Contracts\Generator\StepCounterInterface;
use ApiGen\Contracts\Generator\TemplateGenerators\TemplateGeneratorInterface;
use ApiGen\Contracts\Parser\Elements\ElementStorageInterface;
use ApiGen\Generator\Event\GenerateProgressEvent;
use ApiGen\Parser\Elements\Elements;
use ApiGen\Templating\Template;
use ApiGen\Templating\TemplateFactory;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class NamespaceGenerator implements TemplateGeneratorInterface, StepCounterInterface
{
    /**
     * @var TemplateFactory
     */
    private $templateFactory;

    /**
     * @var ElementStorageInterface
     */
    private $elementStorage;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    public function __construct(
        TemplateFactory $templateFactory,
        ElementStorageInterface $elementStorage,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->templateFactory = $templateFactory;
        $this->elementStorage = $elementStorage;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function generate(): void
    {
        foreach ($this->elementStorage->getNamespaces() as $name => $namespace) {
            $template = $this->templateFactory->createNamedForElement(TemplateFactory::ELEMENT_NAMESPACE, $name);
            $this->loadTemplateWithNamespace($template, $name, $namespace);
            $template->save();

            $this->eventDispatcher->dispatch(GenerateProgressEvent::class);
        }
    }

    public function getStepCount(): int
    {
        return count($this->elementStorage->getNamespaces());
    }

    /**
     * @param Template $template
     * @param string $name
     * @param mixed[] $elementsInNamespace
     */
    public function loadTemplateWithNamespace(Template $template, string $name, array $elementsInNamespace): void
    {
        $template->setParameters([
            'namespace' => $name,
            'subnamespaces' => $this->getSubnamesForName($name, $template->getParameters()['namespaces'])
        ]);

        $template->setParameters([
            Elements::CLASSES => $elementsInNamespace[Elements::CLASSES],
            Elements::INTERFACES => $elementsInNamespace[Elements::INTERFACES],
            Elements::TRAITS => $elementsInNamespace[Elements::TRAITS],
            Elements::EXCEPTIONS => $elementsInNamespace[Elements::EXCEPTIONS],
            Elements::FUNCTIONS => $elementsInNamespace[Elements::FUNCTIONS]
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
