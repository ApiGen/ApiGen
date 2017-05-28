<?php declare(strict_types=1);

namespace ApiGen\Annotation\AnnotationSubscriber;

use ApiGen\Contracts\Annotation\AnnotationSubscriberInterface;
use ApiGen\Reflection\Contract\Reflection\AbstractReflectionInterface;
use ApiGen\StringRouting\Route\ReflectionRoute;
use ApiGen\Templating\Filters\Helpers\LinkBuilder;
use phpDocumentor\Reflection\DocBlock\Tag;
use phpDocumentor\Reflection\DocBlock\Tags\See;

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

    public function __construct(ReflectionRoute $reflectionRoute, LinkBuilder $linkBuilder)
    {
        $this->reflectionRoute = $reflectionRoute;
        $this->linkBuilder = $linkBuilder;
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
        return '';
        dump($seeTag);
        die;

        // @todo

        return '';
    }
}
