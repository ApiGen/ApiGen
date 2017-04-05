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
        foreach ($this->elementStorage->getClassElements() as $name => $reflectionClass) {
            $template = $this->templateFactory->createForReflection($reflectionClass);
            $this->loadTemplateWithParameters($template, $reflectionClass);
            $template->save();

            $this->eventDispatcher->dispatch(GenerateProgressEvent::class);
        }
    }

    public function getStepCount(): int
    {
        return count($this->elementStorage->getClassElements());
    }

    private function loadTemplateWithParameters(Template $template, ClassReflectionInterface $class): void
    {
        $template = $this->namespaceLoader->loadTemplateWithElementNamespace($template, $class);
        $template->setParameters([
            'class' => $class,
            'tree' => array_merge(array_reverse($class->getParentClasses()), [$class]),
            'directSubClasses' => $class->getDirectSubClasses(),
            'indirectSubClasses' => $class->getIndirectSubClasses(),
            'directImplementers' => $class->getDirectImplementers(),
            'indirectImplementers' => $class->getIndirectImplementers(),
            'directUsers' => $class->getDirectUsers(),
            'indirectUsers' => $class->getIndirectUsers(),
        ]);
    }
}
