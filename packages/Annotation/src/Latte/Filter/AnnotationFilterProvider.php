<?php declare(strict_types=1);

namespace ApiGen\Annotation\Latte\Filter;

use ApiGen\Annotation\AnnotationDecorator;
use ApiGen\Contract\Templating\FilterProviderInterface;
use ApiGen\Event\ProcessDocTextEvent;
use ApiGen\Reflection\Contract\Reflection\AbstractReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Partial\AnnotationsInterface;
use Nette\InvalidArgumentException;
use phpDocumentor\Reflection\DocBlock\Tag;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class AnnotationFilterProvider implements FilterProviderInterface
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
            // use in .latte: {$parameter|description}
            'description' => function (AnnotationsInterface $reflection) {
                $processDocTextEvent = new ProcessDocTextEvent($reflection->getDescription(), $reflection);
                $this->eventDispatcher->dispatch(ProcessDocTextEvent::class, $processDocTextEvent);

                return $processDocTextEvent->getText();
            },

            // use in .latte: {$description|annotation: 'deprecated' :$methodReflection|noescape}
            'annotation' => function ($annotation, AbstractReflectionInterface $reflection): string {
                return $this->annotationDecorator->decorate($annotation, $reflection);
            },

            // use in .latte: {var $filteredAnnotations = ($reflection->getAnnotations()|annotationFilter: ['var'])}
            'annotationFilter' => function (array $annotations, array $excludeAnnotations) {
                $this->ensureFilterArgumentsIsTag($annotations, 'annotationFilter');
                /** @var Tag[] $annotations */
                foreach ($annotations as $key => $annotation) {
                    if (! in_array($annotation->getName(), $excludeAnnotations, true)) {
                        continue;
                    }

                    unset($annotations[$key]);
                }

                return $annotations;
            },
        ];
    }

    /**
     * @param mixed[] $annotations
     */
    private function ensureFilterArgumentsIsTag(array $annotations, string $filterName): void
    {
        foreach ($annotations as $annotation) {
            if ($annotation instanceof Tag) {
                continue;
            }

            throw new InvalidArgumentException(sprintf(
                'Argument for filter "%s" has to be type of "%s". "%s" given.',
                $filterName,
                Tag::class,
                is_object($annotation) ? get_class($annotation) : gettype($annotation)
            ));
        }
    }
}
