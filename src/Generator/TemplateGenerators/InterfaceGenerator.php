<?php declare(strict_types=1);

namespace ApiGen\Generator\TemplateGenerators;

use ApiGen\Contracts\Configuration\ConfigurationInterface;
use ApiGen\Contracts\Generator\StepCounterInterface;
use ApiGen\Contracts\Generator\TemplateGenerators\TemplateGeneratorInterface;
use ApiGen\Contracts\Parser\Elements\ElementStorageInterface;
use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Templating\TemplateFactory\TemplateFactoryInterface;
use ApiGen\Generator\Event\GenerateProgressEvent;
use ApiGen\Templating\Filters\UrlFilters;
use ApiGen\Templating\Template;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class InterfaceGenerator implements TemplateGeneratorInterface, StepCounterInterface
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
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    public function __construct(
        TemplateFactoryInterface $templateFactory,
        ElementStorageInterface $elementStorage,
        EventDispatcherInterface $eventDispatcher,
        ConfigurationInterface $configuration
    ) {
        $this->templateFactory = $templateFactory;
        $this->elementStorage = $elementStorage;
        $this->eventDispatcher = $eventDispatcher;
        $this->configuration = $configuration;
    }

    public function generate(): void
    {
        foreach ($this->elementStorage->getInterfaces() as $name => $interfaceReflection) {
            $template = $this->templateFactory->createForReflection($interfaceReflection);
            $this->loadTemplateWithParameters($template, $interfaceReflection);

            $template->save($this->getDestinationPath($interfaceReflection));

            $this->eventDispatcher->dispatch(GenerateProgressEvent::class);
        }
    }

    public function getStepCount(): int
    {
        return count($this->elementStorage->getInterfaces());
    }

    private function getDestinationPath(ClassReflectionInterface $interfaceReflection): string
    {
        $fileName = sprintf(
            'interface-%s.html',
            UrlFilters::urlize($interfaceReflection->getName())
        );

        return $this->configuration->getDestination()
            . DIRECTORY_SEPARATOR
            . $fileName;
    }

    private function loadTemplateWithParameters(Template $template, ClassReflectionInterface $interface): void
    {
        $template->setParameters([
            'interface' => $interface,
            'tree' => array_merge(array_reverse($interface->getParentClasses()), [$interface]),
        ]);
    }
}
