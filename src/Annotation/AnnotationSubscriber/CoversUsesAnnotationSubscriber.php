<?php declare(strict_types=1);

namespace ApiGen\Annotation\AnnotationSubscriber;

use ApiGen\Contracts\Annotation\AnnotationSubscriberInterface;
use ApiGen\Reflection\Contract\Reflection\AbstractReflectionInterface;
use phpDocumentor\Reflection\DocBlock\Tag;
use phpDocumentor\Reflection\DocBlock\Tags\Covers;
use phpDocumentor\Reflection\DocBlock\Tags\Uses;

final class CoversUsesAnnotationSubscriber implements AnnotationSubscriberInterface
{
    /**
     * @var SeeAnnotationSubscriber
     */
    private $seeAnnotationSubscriber;

    public function __construct(SeeAnnotationSubscriber $seeAnnotationSubscriber)
    {
        $this->seeAnnotationSubscriber = $seeAnnotationSubscriber;
    }

    /**
     * @param Tag|string $content
     */
    public function matches($content): bool
    {
        return $content instanceof Covers || $content instanceof Uses;
    }

    /**
     * @param Covers|Uses $content
     */
    public function process($content, AbstractReflectionInterface $reflection): string
    {
        return $this->seeAnnotationSubscriber->process($content, $reflection);
    }
}
