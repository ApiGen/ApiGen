<?php declare(strict_types=1);

namespace ApiGen\EventSubscriber;

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
     * @var NamespaceReflectionCollector
     */
    private $namespaceReflectionCollector;

    public function __construct(
        ReflectionStorage $reflectionStorage,
        NamespaceReflectionCollector $namespaceReflectionCollector
    ) {
        $this->reflectionStorage = $reflectionStorage;
        $this->namespaceReflectionCollector = $namespaceReflectionCollector;
    }

    /**
     * @return string[]
     */
    public static function getSubscribedEvents(): array
    {
        return [
            CreateTemplateEvent::class => 'loadTemplateVariables',
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
            'allClasses' => $this->reflectionStorage->getClassReflections(),
            'allExceptions' => $this->reflectionStorage->getExceptionReflections(),
            'allInterfaces' => $this->reflectionStorage->getInterfaceReflections(),
            'allTraits' => $this->reflectionStorage->getTraitReflections(),
            'allFunctions' => $this->reflectionStorage->getFunctionReflections(),
        ]);
    }
}
