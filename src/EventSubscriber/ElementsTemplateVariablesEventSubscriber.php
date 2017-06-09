<?php declare(strict_types=1);

namespace ApiGen\EventSubscriber;

use ApiGen\Element\AutocompleteElements;
use ApiGen\Element\ReflectionCollector\NamespaceReflectionCollector;
use ApiGen\Event\CreateTemplateEvent;
use ApiGen\Reflection\ReflectionStorage;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class ElementsTemplateVariablesEventSubscriber implements EventSubscriberInterface
{
    /**
     * @var ReflectionStorage
     */
    private $reflectionStorage;

    /**
     * @var AutocompleteElements
     */
    private $autocompleteElements;

    /**
     * @var NamespaceReflectionCollector
     */
    private $namespaceReflectionCollector;

    public function __construct(
        ReflectionStorage $reflectionStorage,
        NamespaceReflectionCollector $namespaceReflectionCollector,
        AutocompleteElements $autocompleteElements
    ) {
        $this->reflectionStorage = $reflectionStorage;
        $this->autocompleteElements = $autocompleteElements;
        $this->namespaceReflectionCollector = $namespaceReflectionCollector;
    }

    /**
     * @return string[]
     */
    public static function getSubscribedEvents(): array
    {
        return [
            CreateTemplateEvent::class => 'loadTemplateVariables'
        ];
    }

    public function loadTemplateVariables(CreateTemplateEvent $createTemplateEvent): void
    {
        $parameterBag = $createTemplateEvent->getParameterBag();
        // add default empty values
        $parameterBag->addParameters([
            'activePage' => null,
            'activeNamespace' => null,
            'activeClass' => null,
            'activeInterface' => null,
            'activeTrait' => null,
            'activeFunction' => null,
        ]);
        // add all available elements (for layout)
        $parameterBag->addParameters([
            'allNamespaces' => $this->namespaceReflectionCollector->getNamespaces(),
            // @todo what is array_filter for? make it explicit!
//            'allClasses' => array_filter($this->reflectionStorage->getClassReflections()),
            'allClasses' => $this->reflectionStorage->getClassReflections(),
            'allInterfaces' => $this->reflectionStorage->getInterfaceReflections(),
            'allTraits' => $this->reflectionStorage->getTraitReflections(),
            'allFunctions' => $this->reflectionStorage->getFunctionReflections(),
            'autocompleteElements' => $this->autocompleteElements->getElements()
        ]);
    }
}
