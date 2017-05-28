<?php declare(strict_types=1);

namespace ApiGen\Contracts\Annotation;

use ApiGen\Reflection\Contract\Reflection\AbstractReflectionInterface;
use phpDocumentor\Reflection\DocBlock\Tag;

interface AnnotationSubscriberInterface
{
    /**
     * @param Tag|string $content
     */
    public function matches($content): bool;

    /**
     * @param Tag|string $content
     */
    public function process($content, AbstractReflectionInterface $reflection): string;
}
