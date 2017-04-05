<?php declare(strict_types=1);

namespace ApiGen\Generator\TemplateGenerators;

use ApiGen\Contracts\Generator\StepCounterInterface;
use ApiGen\Contracts\Generator\TemplateGenerators\TemplateGeneratorInterface;
use ApiGen\Contracts\Parser\Elements\ElementStorageInterface;
use ApiGen\Contracts\Parser\Reflection\FunctionReflectionInterface;
use ApiGen\Contracts\Templating\TemplateFactory\TemplateFactoryInterface;
use ApiGen\Generator\Event\GenerateProgressEvent;
use ApiGen\Generator\TemplateGenerators\Loaders\NamespaceLoader;
use ApiGen\Templating\Template;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class FunctionElementGenerator implements TemplateGeneratorInterface, StepCounterInterface
{
    /**
     * @var TemplateFactoryInterface
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
        TemplateFactoryInterface $templateFactory,
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
        foreach ($this->elementStorage->getFunctions() as $name => $reflectionFunction) {
            $template = $this->templateFactory->createForReflection($reflectionFunction);
            $this->loadTemplateWithParameters($template, $reflectionFunction);
            $template->save();

            $this->eventDispatcher->dispatch(GenerateProgressEvent::class);
        }
    }

    public function getStepCount(): int
    {
        return count($this->elementStorage->getFunctions());
    }

    private function loadTemplateWithParameters(Template $template, FunctionReflectionInterface $function): void
    {
        $template = $this->namespaceLoader->loadTemplateWithElementNamespace($template, $function);
        $template->setParameters([
            'function' => $function
        ]);
    }
}
