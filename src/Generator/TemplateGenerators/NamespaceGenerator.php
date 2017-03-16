<?php declare(strict_types=1);

namespace ApiGen\Generator\TemplateGenerators;

use ApiGen\Contracts\Generator\StepCounterInterface;
use ApiGen\Contracts\Generator\TemplateGenerators\TemplateGeneratorInterface;
use ApiGen\Contracts\Parser\Elements\ElementStorageInterface;
use ApiGen\Generator\Event\GenerateProgressEvent;
use ApiGen\Generator\TemplateGenerators\Loaders\NamespaceLoader;
use ApiGen\Templating\TemplateFactory;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class NamespaceGenerator implements TemplateGeneratorInterface, StepCounterInterface
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
     * @var NamespaceLoader
     */
    private $namespaceLoader;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;


    public function __construct(
        TemplateFactory $templateFactory,
        ElementStorageInterface $elementStorage,
        NamespaceLoader $namespaceLoader,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->templateFactory = $templateFactory;
        $this->elementStorage = $elementStorage;
        $this->namespaceLoader = $namespaceLoader;
        $this->eventDispatcher = $eventDispatcher;
    }


    public function generate(): void
    {
        foreach ($this->elementStorage->getNamespaces() as $name => $namespace) {
            $template = $this->templateFactory->createNamedForElement(TemplateFactory::ELEMENT_NAMESPACE, $name);
            $template = $this->namespaceLoader->loadTemplateWithNamespace($template, $name, $namespace);
            $template->save();

            $this->eventDispatcher->dispatch(GenerateProgressEvent::class);
        }
    }


    public function getStepCount(): int
    {
        return count($this->elementStorage->getNamespaces());
    }
}
