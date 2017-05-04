<?php declare(strict_types=1);

namespace ApiGen\EventSubscriber;

use ApiGen\Contracts\Parser\Elements\ElementStorageInterface;
use ApiGen\Event\CreateTemplateEvent;
use ApiGen\Parser\Elements\AutocompleteElements;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class ElementsTemplateVariablesEventSubscriber implements EventSubscriberInterface
{
    /**
     * @var ElementStorageInterface
     */
    private $elementStorage;

    /**
     * @var AutocompleteElements
     */
    private $autocompleteElements;

    public function __construct(ElementStorageInterface $elementStorage, AutocompleteElements $autocompleteElements)
    {
        $this->elementStorage = $elementStorage;
        $this->autocompleteElements = $autocompleteElements;
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
        $parameterBag->addParameters([
            'namespace' => null,
            'class' => null,
            'function' => null,
            'namespaces' => array_keys($this->elementStorage->getNamespaces()),
            'classes' => array_filter($this->elementStorage->getClasses()),
            'interfaces' => array_filter($this->elementStorage->getInterfaces()),
            'traits' => array_filter($this->elementStorage->getTraits()),
            'functions' => array_filter($this->elementStorage->getFunctions()),
            'elements' => $this->autocompleteElements->getElements()
        ]);
    }
}
