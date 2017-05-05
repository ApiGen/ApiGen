<?php declare(strict_types=1);

namespace ApiGen\EventSubscriber;

use ApiGen\Event\CreateTemplateEvent;
use ApiGen\Namespaces\NamespaceStorage;
use ApiGen\Parser\Elements\AutocompleteElements;
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
        $parameterBag->addParameters([
            'namespace' => null,
            'class' => null,
            'function' => null,
            'namespaces' => array_keys($this->namespaceStorage->categorizeReflectionsToNamespaces()),
            'classes' => array_filter($this->reflectionStorage->getClassReflections()),
            'interfaces' => array_filter($this->reflectionStorage->getInterfaceReflections()),
            'traits' => array_filter($this->reflectionStorage->getTraitReflections()),
            'functions' => array_filter($this->reflectionStorage->getFunctionReflections()),
            'elements' => $this->autocompleteElements->getElements() // @todo: rename to autocompleteElements
        ]);
    }
}
