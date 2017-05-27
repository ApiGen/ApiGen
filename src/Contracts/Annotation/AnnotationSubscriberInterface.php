<?php declare(strict_types=1);

namespace ApiGen\Contracts\Annotation;

use ApiGen\Reflection\Contract\Reflection\AbstractReflectionInterface;
use phpDocumentor\Reflection\DocBlock\Tag;

interface AnnotationSubscriberInterface
{
    public function getAnnotation(): string;

    public function process(Tag $tag, AbstractReflectionInterface $reflection): string;
}
