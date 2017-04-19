<?php declare(strict_types=1);

namespace ApiGen\Generator\TemplateGenerators;

use ApiGen\Contracts\Generator\StepCounterInterface;
use ApiGen\Contracts\Generator\TemplateGenerators\TemplateGeneratorInterface;
use ApiGen\Contracts\Parser\Elements\ElementStorageInterface;
use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Templating\TemplateFactory\TemplateFactoryInterface;
use ApiGen\Generator\Event\GenerateProgressEvent;
use ApiGen\Templating\Template;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class TraitGenerator implements TemplateGeneratorInterface, StepCounterInterface
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

    public function __construct(
        TemplateFactoryInterface $templateFactory,
        ElementStorageInterface $elementStorage,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->templateFactory = $templateFactory;
        $this->elementStorage = $elementStorage;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function generate(): void
    {
        foreach ($this->elementStorage->getTraits() as $name => $traitReflection) {
            $template = $this->templateFactory->createForReflection($traitReflection);
            $this->loadTemplateWithParameters($template, $traitReflection);
            $template->save();

            $this->eventDispatcher->dispatch(GenerateProgressEvent::class);
        }
    }

    public function getStepCount(): int
    {
        return count($this->elementStorage->getTraits());
    }

    private function loadTemplateWithParameters(Template $template, ClassReflectionInterface $trait): void
    {
        $template->setParameters([
            'trait' => $trait,
            'tree' => array_merge(array_reverse($trait->getParentClasses()), [$trait]),
        ]);
    }
}
