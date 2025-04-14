<?php declare(strict_types=1);

namespace ApiGen\Annotation\AnnotationSubscriber;

use ApiGen\Annotation\Contract\AnnotationSubscriber\AnnotationSubscriberInterface;
use ApiGen\Reflection\Contract\Reflection\AbstractReflectionInterface;
use phpDocumentor\Reflection\DocBlock\Tag;
use phpDocumentor\Reflection\DocBlock\Tags\Author;

final class AuthorAnnotationSubscriber implements AnnotationSubscriberInterface
{
    /**
     * @param Tag|string $content
     */
    public function matches($content): bool
    {
        return $content instanceof Author;
    }

    /**
     * @param Author $content
     */
    public function process($content, AbstractReflectionInterface $reflection): string
    {
        return htmlentities((string) $content);
    }
}
