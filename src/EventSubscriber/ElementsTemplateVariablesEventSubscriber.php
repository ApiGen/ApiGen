<?php declare(strict_types=1);

namespace ApiGen\EventSubscriber;

use ApiGen\Event\CreateTemplateEvent;
use ApiGen\Element\Namespaces\NamespaceStorage;
use ApiGen\Element\AutocompleteElements;
use ApiGen\Reflection\Contract\ReflectionStorageInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class ElementsTemplateVariablesEventSubscriber implements EventSubscriberInterface
{
    /**
     * @var ReflectionStorageInterface
     */
    private $reflectionStorage;

    /**
     * @var AutocompleteElements
     */
    private $autocompleteElements;

    /**
     * @var NamespaceStorage
     */
    private $namespaceStorage;

    public function __construct(
        ReflectionStorageInterface $reflectionStorage,
        NamespaceStorage $namespaceStorage,
        AutocompleteElements $autocompleteElements
    ) {
        $this->reflectionStorage = $reflectionStorage;
        $this->autocompleteElements = $autocompleteElements;
        $this->namespaceStorage = $namespaceStorage;
    }

    /**
     * @return string[]
     */
    public static function getSubscribedEvents(): array
    {
        return [CreateTemplateEvent::class => 'loadTemplateVariables'];
    }

    public function loadTemplateVariables(CreateTemplateEvent $createTemplateEvent): void
    {
        $parameterBag = $createTemplateEvent->getParameterBag();
        // add default empty values
        $parameterBag->addParameters([
            'activePage' => null,
            'activeNamespace' => null,
            'activeClass' => null,
            'activeFunction' => null,
        ]);
        // add all available elements (for layout)
        $parameterBag->addParameters([
            'allNamespaces' => $this->namespaceStorage->getNamespaces(),
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
