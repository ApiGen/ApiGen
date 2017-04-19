<?php declare(strict_types=1);

namespace ApiGen\Generator\TemplateGenerators;

use ApiGen\Contracts\Generator\StepCounterInterface;
use ApiGen\Contracts\Generator\TemplateGenerators\TemplateGeneratorInterface;
use ApiGen\Contracts\Parser\Elements\ElementStorageInterface;
use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Templating\TemplateFactory\TemplateFactoryInterface;
use ApiGen\Generator\Event\GenerateProgressEvent;
use ApiGen\Generator\TemplateGenerators\Loaders\NamespaceLoader;
use ApiGen\Templating\Template;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class ClassElementGenerator implements TemplateGeneratorInterface, StepCounterInterface
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
        foreach ($this->elementStorage->getClasses() as $name => $classReflection) {
            $template = $this->templateFactory->createForReflection($classReflection);
            $this->loadTemplateWithParameters($template, $classReflection);
            $template->save();

            $this->eventDispatcher->dispatch(GenerateProgressEvent::class);
        }

        foreach ($this->elementStorage->getTraits() as $name => $classReflection) {
            $template = $this->templateFactory->createForReflection($classReflection);
            $this->loadTemplateWithParameters($template, $classReflection);
            $template->save();

            $this->eventDispatcher->dispatch(GenerateProgressEvent::class);
        }

        foreach ($this->elementStorage->getInterfaces() as $name => $classReflection) {
            $template = $this->templateFactory->createForReflection($classReflection);
            $this->loadTemplateWithParameters($template, $classReflection);
            $template->save();

            $this->eventDispatcher->dispatch(GenerateProgressEvent::class);
        }

        foreach ($this->elementStorage->getExceptions() as $name => $classReflection) {
            $template = $this->templateFactory->createForReflection($classReflection);
            $this->loadTemplateWithParameters($template, $classReflection);
            $template->save();

            $this->eventDispatcher->dispatch(GenerateProgressEvent::class);
        }
    }

    public function getStepCount(): int
    {
        return count($this->elementStorage->getClasses())
            + count($this->elementStorage->getInterfaces())
            + count($this->elementStorage->getTraits())
            + count($this->elementStorage->getExceptions());
    }

    private function loadTemplateWithParameters(Template $template, ClassReflectionInterface $class): void
    {
        $template = $this->namespaceLoader->loadTemplateWithElementNamespace($template, $class);
        $template->setParameters([
            'class' => $class,
            'trait' => $class,
            'interface' => $class,
            'tree' => array_merge(array_reverse($class->getParentClasses()), [$class]),

            // class
            'directSubClasses' => $class->getDirectSubClasses(),
            'indirectSubClasses' => $class->getIndirectSubClasses(),

            // interface
            'directImplementers' => $class->getDirectImplementers(),
            'indirectImplementers' => $class->getIndirectImplementers(),

            // trait
            'directUsers' => $class->getDirectUsers(),
            'indirectUsers' => $class->getIndirectUsers(),
        ]);
    }
}
