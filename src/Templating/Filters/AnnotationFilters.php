<?php declare(strict_types=1);

namespace ApiGen\Templating\Filters;

use ApiGen\Event\FilterAnnotationsEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symplify\ModularLatteFilters\Contract\DI\LatteFiltersProviderInterface;

final class AnnotationFilters implements LatteFiltersProviderInterface
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
     * @return callable[]
     */
    public function getFilters(): array
    {
        return [
            'annotationFilter' => function (array $annotations, array $annotationsToRemove = []) {
                $filterAnnotationsEvent = new FilterAnnotationsEvent($annotations);
                $this->eventDispatcher->dispatch(FilterAnnotationsEvent::class, $filterAnnotationsEvent);
                $annotations = $filterAnnotationsEvent->getAnnotations();

                foreach ($annotationsToRemove as $annotationToRemove) {
                    unset($annotations[$annotationToRemove]);
                }

                return $annotations;
            }
        ];
    }
}
