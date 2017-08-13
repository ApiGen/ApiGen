<?php declare(strict_types=1);

namespace ApiGen\Annotation;

use ApiGen\Annotation\Contract\AnnotationSubscriber\AnnotationSubscriberInterface;
use ApiGen\Reflection\Contract\Reflection\AbstractReflectionInterface;
use phpDocumentor\Reflection\DocBlock\Tag;

/**
 * @see https://github.com/phpDocumentor/TypeResolver#resolving-an-fqsen
 */
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

    /**
     * @param Tag|string $content
     */
    public function decorate($content, AbstractReflectionInterface $reflection): string
    {
        foreach ($this->annotationSubscribers as $annotationSubscriber) {
            if ($annotationSubscriber->matches($content)) {
                return $annotationSubscriber->process($content, $reflection);
            }
        }

        return '';
    }
}
