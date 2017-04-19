<?php declare(strict_types=1);

namespace ApiGen\Generator\TemplateGenerators;

use ApiGen\Contracts\Configuration\ConfigurationInterface;
use ApiGen\Contracts\Generator\StepCounterInterface;
use ApiGen\Contracts\Generator\TemplateGenerators\TemplateGeneratorInterface;
use ApiGen\Contracts\Parser\Elements\ElementStorageInterface;
use ApiGen\Contracts\Parser\Reflection\FunctionReflectionInterface;
use ApiGen\Contracts\Templating\TemplateFactory\TemplateFactoryInterface;
use ApiGen\Generator\Event\GenerateProgressEvent;
use ApiGen\Templating\Filters\Filters;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class FunctionGenerator implements TemplateGeneratorInterface, StepCounterInterface
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
        foreach ($this->elementStorage->getFunctions() as $name => $reflectionFunction) {
            $template = $this->templateFactory->createForReflection($reflectionFunction);

            $template->setParameters([
                'function' => $reflectionFunction
            ]);

            $template->save($this->getDestination($reflectionFunction));

            $this->eventDispatcher->dispatch(GenerateProgressEvent::class);
        }
    }

    public function getStepCount(): int
    {
        return count($this->elementStorage->getFunctions());
    }

    private function getDestination(FunctionReflectionInterface $reflectionFunction): string
    {
        return $this->configuration->getDestination()
            . DIRECTORY_SEPARATOR
            . Filters::urlize(sprintf(
                'function-%s.html',
                $reflectionFunction->getName()
            ));
    }
}
