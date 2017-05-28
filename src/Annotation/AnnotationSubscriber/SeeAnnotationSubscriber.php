<?php declare(strict_types=1);

namespace ApiGen\Annotation\AnnotationSubscriber;

use ApiGen\Annotation\FqsenResolver\ElementResolver;
use ApiGen\Contracts\Annotation\AnnotationSubscriberInterface;
use ApiGen\Reflection\Contract\Reflection\AbstractReflectionInterface;
use ApiGen\StringRouting\Route\ReflectionRoute;
use ApiGen\Templating\Filters\Helpers\LinkBuilder;
use phpDocumentor\Reflection\DocBlock\Tag;
use phpDocumentor\Reflection\DocBlock\Tags\See;
use phpDocumentor\Reflection\Fqsen;

final class SeeAnnotationSubscriber implements AnnotationSubscriberInterface
{
    /**
     * @var ReflectionRoute
     */
    private $reflectionRoute;

    /**
     * @var LinkBuilder
     */
    private $linkBuilder;
    /**
     * @var ElementResolver
     */
    private $elementResolver;

    public function __construct(ReflectionRoute $reflectionRoute, LinkBuilder $linkBuilder, ElementResolver $elementResolver)
    {
        $this->reflectionRoute = $reflectionRoute;
        $this->linkBuilder = $linkBuilder;
        $this->elementResolver = $elementResolver;
    }

    public function getAnnotation(): string
    {
        return See::class;
    }

    /**
     * @param See $seeTag
     * @return string
     */
    public function process(Tag $seeTag, AbstractReflectionInterface $reflection): string
    {
        if ($seeTag->getReference() instanceof Fqsen) {
            $resolvedReflection = $this->elementResolver->resolveReflectionFromNameAndReflection(
                (string) $seeTag->getReference(), $reflection
            );

            $url = $this->reflectionRoute->constructUrl($resolvedReflection);

            return '<code>' . $this->linkBuilder->build($url, ltrim((string) $seeTag->getReference(), '\\')) . '</code>';
        }

        // @todo

        return '';
    }
}
