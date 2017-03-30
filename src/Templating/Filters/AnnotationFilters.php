<?php declare(strict_types=1);

namespace ApiGen\Templating\Filters;

use ApiGen\Event\FilterAnnotationsEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class AnnotationFilters extends Filters
{
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
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

        return $annotations;
    }
}
