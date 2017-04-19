<?php declare(strict_types=1);

namespace ApiGen\Generator\TemplateGenerators;

use ApiGen\Contracts\Configuration\ConfigurationInterface;
use ApiGen\Contracts\Generator\StepCounterInterface;
use ApiGen\Contracts\Generator\TemplateGenerators\TemplateGeneratorInterface;
use ApiGen\Contracts\Parser\Elements\ElementStorageInterface;
use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Templating\TemplateFactory\TemplateFactoryInterface;
use ApiGen\Generator\Event\GenerateProgressEvent;
use ApiGen\Generator\TemplateGenerators\Loaders\NamespaceLoader;
use ApiGen\Templating\Filters\UrlFilters;
use ApiGen\Templating\Template;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class ClassGenerator implements TemplateGeneratorInterface, StepCounterInterface
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

    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    public function __construct(
        TemplateFactoryInterface $templateFactory,
        ElementStorageInterface $elementStorage,
        NamespaceLoader $namespaceLoader,
        EventDispatcherInterface $eventDispatcher,
        ConfigurationInterface $configuration
    ) {
        $this->templateFactory = $templateFactory;
        $this->elementStorage = $elementStorage;
        $this->namespaceLoader = $namespaceLoader;
        $this->eventDispatcher = $eventDispatcher;
        $this->configuration = $configuration;
    }

    public function generate(): void
    {
        foreach ($this->elementStorage->getClasses() as $name => $classReflection) {
            $template = $this->templateFactory->createForReflection($classReflection);
            $this->loadTemplateWithParameters($template, $classReflection);

            $template->setSavePath($this->getDestinationPath($classReflection));
            $template->save();

            $this->eventDispatcher->dispatch(GenerateProgressEvent::class);
        }
    }

    public function getStepCount(): int
    {
        return count($this->elementStorage->getClasses());
    }

    private function getDestinationPath(ClassReflectionInterface $classReflection): string
    {
        $fileName = sprintf(
            'class-%s.html',
            UrlFilters::urlize($classReflection->getName())
        );

        return $this->configuration->getDestination()
            . DIRECTORY_SEPARATOR
            . $fileName;
    }

    private function loadTemplateWithParameters(Template $template, ClassReflectionInterface $class): void
    {
        $template = $this->namespaceLoader->loadTemplateWithElementNamespace($template, $class);
        $template->setParameters([
            'class' => $class,
            'tree' => array_merge(array_reverse($class->getParentClasses()), [$class]),
        ]);
    }
}
