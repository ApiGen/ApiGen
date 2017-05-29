<?php declare(strict_types=1);

namespace ApiGen\Templating\Filters;

use ApiGen\Annotation\AnnotationDecorator;
use ApiGen\Event\FilterAnnotationsEvent;
use ApiGen\Event\ProcessDocTextEvent;
use ApiGen\Reflection\Contract\Reflection\AbstractReflectionInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symplify\ModularLatteFilters\Contract\DI\LatteFiltersProviderInterface;

final class AnnotationFilters implements LatteFiltersProviderInterface
{
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var AnnotationDecorator
     */
    private $annotationDecorator;

    public function __construct(EventDispatcherInterface $eventDispatcher, AnnotationDecorator $annotationDecorator)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->annotationDecorator = $annotationDecorator;
    }

    /**
     * @return callable[]
     */
    public function getFilters(): array
    {
        return [
            // use in .latte: {$parameter->getDescription()|description: $function}
            'description' => function ($description, AbstractReflectionInterface $reflection) {
                // todo: maybe pass reflection directly
                $description = trim(strpbrk($description, "\n\r\t $")) ?: $description;

                $processDocTextEvent = new ProcessDocTextEvent($description, $reflection);
                $this->eventDispatcher->dispatch(ProcessDocTextEvent::class, $processDocTextEvent);
                return $processDocTextEvent->getText();
            },

            // use in .latte: {$description|annotation: 'deprecated' :$methodReflection|noescape}
            'annotation' => function ($annotation, AbstractReflectionInterface $reflection): string {
                return $this->annotationDecorator->decorate($annotation, $reflection);
            },

            // use in .latte: {var $filteredAnnotations = ($reflection->getAnnotations()|annotationFilter: ['var'])}
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
