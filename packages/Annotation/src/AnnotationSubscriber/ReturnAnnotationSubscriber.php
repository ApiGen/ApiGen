<?php declare(strict_types=1);

namespace ApiGen\Annotation\AnnotationSubscriber;

use ApiGen\Annotation\Contract\AnnotationSubscriber\AnnotationSubscriberInterface;
use ApiGen\Annotation\FqsenResolver\ElementResolver;
use ApiGen\Reflection\Contract\Reflection\AbstractReflectionInterface;
use ApiGen\StringRouting\Route\ReflectionRoute;
use ApiGen\Utils\LinkBuilder;
use phpDocumentor\Reflection\DocBlock\Tag;
use phpDocumentor\Reflection\DocBlock\Tags\Return_;
use phpDocumentor\Reflection\Types\Array_;
use phpDocumentor\Reflection\Types\Object_;

final class ReturnAnnotationSubscriber implements AnnotationSubscriberInterface
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
        return $content instanceof Return_;
    }

    /**
     * @param Return_ $content
     */
    public function process($content, AbstractReflectionInterface $reflection): string
    {
        if ($content->getType() instanceof Array_) {
            /** @var Array_ $arrayType */
            $arrayType = $content->getType();
            if ($arrayType->getValueType() instanceof Object_) {
                /** @var Object_ $objectValueType */
                $objectValueType = $arrayType->getValueType();
                $link = $this->createLinkFromObject($reflection, $objectValueType);

                return '<code>' . $link . '[]</code>';
            }
        } elseif ($content->getType() instanceof Object_) {
            /** @var Object_ $objectValueType */
            $objectValueType = $content->getType();
            $link = $this->createLinkFromObject($reflection, $objectValueType);

            return '<code>' . $link . '</code>';
        }

        // @todo

        return '';
    }

    private function createLinkFromObject(AbstractReflectionInterface $reflection, Object_ $objectValueType): string
    {
        $type = $objectValueType->getFqsen()
            ->getName();

        $singleClassReflection = $this->elementResolver->resolveReflectionFromNameAndReflection($type, $reflection);
        $classUrl = $this->reflectionRoute->constructUrl($singleClassReflection);

        return $this->linkBuilder->build($classUrl, $type);
    }
}
