<?php declare(strict_types=1);

namespace ApiGen\Templating\Filters;

use ApiGen\Configuration\ConfigurationOptions;
use ApiGen\Contracts\Configuration\ConfigurationInterface;
use ApiGen\Event\FilterAnnotationsEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class AnnotationFilters extends Filters
{
    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    public function __construct(ConfigurationInterface $configuration, EventDispatcherInterface $eventDispatcher)
    {
        $this->configuration = $configuration;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param string[] $annotations
     * @param string[] $annotationsToRemove
     * @return string[]
     */
    public function annotationFilter(array $annotations, array $annotationsToRemove = []): array
    {
        $filterAnnotationsEvent = new FilterAnnotationsEvent($annotations);
        $this->eventDispatcher->dispatch(FilterAnnotationsEvent::class, $filterAnnotationsEvent);
        $annotations = $filterAnnotationsEvent->getAnnotations();

        foreach ($annotationsToRemove as $annotationToRemove) {
            unset($annotations[$annotationToRemove]);
        }

        if (! $this->configuration->getOption(ConfigurationOptions::INTERNAL)) {
            unset($annotations['internal']);
        }

        return $annotations;
    }
}
