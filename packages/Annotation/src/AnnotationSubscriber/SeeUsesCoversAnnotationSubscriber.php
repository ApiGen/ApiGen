<?php declare(strict_types=1);

namespace ApiGen\Annotation\AnnotationSubscriber;

use ApiGen\Annotation\FqsenResolver\ElementResolver;
use ApiGen\Contracts\Annotation\AnnotationSubscriberInterface;
use ApiGen\Reflection\Contract\Reflection\AbstractReflectionInterface;
use ApiGen\StringRouting\Route\ReflectionRoute;
use ApiGen\Templating\Filters\Helpers\LinkBuilder;
use phpDocumentor\Reflection\DocBlock\Tag;
use phpDocumentor\Reflection\DocBlock\Tags\Covers;
use phpDocumentor\Reflection\DocBlock\Tags\See;
use phpDocumentor\Reflection\DocBlock\Tags\Uses;
use phpDocumentor\Reflection\Fqsen;

final class SeeUsesCoversAnnotationSubscriber implements AnnotationSubscriberInterface
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

    public function __construct(
        ReflectionRoute $reflectionRoute,
        LinkBuilder $linkBuilder,
        ElementResolver $elementResolver
    ) {
        $this->reflectionRoute = $reflectionRoute;
        $this->linkBuilder = $linkBuilder;
        $this->elementResolver = $elementResolver;
    }

    /**
     * @param Tag|string $content
     */
    public function matches($content): bool
    {
        return $content instanceof See || $content instanceof Covers || $content instanceof Uses;
    }

    /**
     * @param See|Covers|Uses $content
     */
    public function process($content, AbstractReflectionInterface $reflection): string
    {
        if ($content->getReference() instanceof Fqsen) {
            $reference = (string) $content->getReference();
            $reference = ltrim($reference, '\\');

            $resolvedReflection = $this->elementResolver->resolveReflectionFromNameAndReflection(
                $reference, $reflection
            );

            if ($resolvedReflection instanceof AbstractReflectionInterface) {
                $url = $this->reflectionRoute->constructUrl($resolvedReflection);
                return '<code>' . $this->linkBuilder->build($url, ltrim($reference, '\\')) . '</code>';
            }

            return '<code>' . $reference . '</code>';
        }

        // @todo

        return '';
    }
}
