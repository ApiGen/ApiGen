<?php declare(strict_types=1);

namespace ApiGen\Annotation;

use ApiGen\Contracts\Annotation\AnnotationSubscriberInterface;
use ApiGen\Reflection\Contract\Reflection\AbstractReflectionInterface;
use ApiGen\Reflection\Contract\ReflectionStorageInterface;
use ApiGen\StringRouting\Route\ReflectionRoute;
use ApiGen\Templating\Filters\Helpers\LinkBuilder;
use phpDocumentor\Reflection\DocBlock\Tag;

# see: https://github.com/phpDocumentor/TypeResolver#resolving-an-fqsen

final class AnnotationDecorator
{
    /**
     * @var AnnotationSubscriberInterface[]
     */
    private $annotationSubscribers = [];

    public function addAnnotationSubscriber(AnnotationSubscriberInterface $annotationSubscriber): void
    {
        $this->annotationSubscribers[] = $annotationSubscriber;
    }

    public function decorate(Tag $tag, AbstractReflectionInterface $reflection): string
    {
        foreach ($this->annotationSubscribers as $annotationSubscriber) {
            if (is_a($tag, $annotationSubscriber->getAnnotation())) {
                return $annotationSubscriber->process($tag, $reflection);
            }
        }

        return '';
    }
}
