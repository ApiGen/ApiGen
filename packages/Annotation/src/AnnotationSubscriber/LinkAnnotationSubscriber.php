<?php declare(strict_types=1);

namespace ApiGen\Annotation\AnnotationSubscriber;

use ApiGen\Annotation\Contract\AnnotationSubscriber\AnnotationSubscriberInterface;
use ApiGen\Reflection\Contract\Reflection\AbstractReflectionInterface;
use ApiGen\Reflection\DocBlock\Tags\See;
use ApiGen\Utils\LinkBuilder;
use phpDocumentor\Reflection\DocBlock\Tag;
use phpDocumentor\Reflection\DocBlock\Tags\Link;

final class LinkAnnotationSubscriber implements AnnotationSubscriberInterface
{
    /**
     * @var LinkBuilder
     */
    private $linkBuilder;

    public function __construct(LinkBuilder $linkBuilder)
    {
        $this->linkBuilder = $linkBuilder;
    }

    /**
     * @param Tag|string $content
     */
    public function matches($content): bool
    {
        return $content instanceof Link || ($content instanceof See && $content->getLink());
    }

    /**
     * @param Link|See $content
     */
    public function process($content, AbstractReflectionInterface $reflection): string
    {
        return $this->linkBuilder->build(
            $content->getLink(),
            $content->getDescription() ?: $content->getLink()
        );
    }
}
