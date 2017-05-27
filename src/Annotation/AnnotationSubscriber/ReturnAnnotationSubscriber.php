<?php declare(strict_types=1);

namespace ApiGen\Annotation\AnnotationSubscriber;

use ApiGen\Contracts\Annotation\AnnotationSubscriberInterface;
use ApiGen\Reflection\Contract\Reflection\AbstractReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Class_\ClassMethodReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Class_\ClassReflectionInterface;
use ApiGen\Reflection\Contract\ReflectionStorageInterface;
use ApiGen\StringRouting\Route\ReflectionRoute;
use ApiGen\Templating\Filters\Helpers\LinkBuilder;
use phpDocumentor\Reflection\DocBlock\Tag;
use phpDocumentor\Reflection\DocBlock\Tags\Return_;
use phpDocumentor\Reflection\FqsenResolver;
use phpDocumentor\Reflection\Types\Array_;
use phpDocumentor\Reflection\Types\ContextFactory;
use phpDocumentor\Reflection\Types\Object_;
use ReflectionClass;

final class ReturnAnnotationSubscriber implements AnnotationSubscriberInterface
{
    /**
     * @var ReflectionStorageInterface
     */
    private $reflectionStorage;

    /**
     * @var ReflectionRoute
     */
    private $reflectionRoute;

    /**
     * @var LinkBuilder
     */
    private $linkBuilder;

    public function __construct(
        ReflectionStorageInterface $reflectionStorage,
        ReflectionRoute $reflectionRoute,
        LinkBuilder $linkBuilder
    ) {
        $this->reflectionStorage = $reflectionStorage;
        $this->reflectionRoute = $reflectionRoute;
        $this->linkBuilder = $linkBuilder;
    }

    public function getAnnotation(): string
    {
        return Return_::class;
    }

    /**
     * @param Return_ $paramTag
     * @return string
     */
    public function process(Tag $paramTag, AbstractReflectionInterface $reflection): string
    {
        if ($paramTag->getType() instanceof Array_) {
            /** @var Array_ $arrayType */
            $arrayType = $paramTag->getType();
            if ($arrayType->getValueType() instanceof Object_) {
                /** @var Object_ $objectValueType */
                $objectValueType = $arrayType->getValueType();
                $type = (string) $objectValueType->getFqsen()
                    ->getName();

                $singleClassReflection = $this->resolveReflectionFromNameAndReflection($type, $reflection);
                $classUrl = $this->reflectionRoute->constructUrl($singleClassReflection);
                return '<code>' . $this->linkBuilder->build($classUrl, $type) . '[]</code>';
            }
        }

        // @todo

        return '';
    }

    private function resolveReflectionFromNameAndReflection(string $name, AbstractReflectionInterface $reflection): ClassReflectionInterface
    {
        if ($reflection instanceof ClassMethodReflectionInterface) {
            $reflectionName = $reflection->getDeclaringClassName();
        }

        $context = (new ContextFactory)->createFromReflector(new ReflectionClass($reflectionName));
        $classReflectionName = (string) (new FqsenResolver)->resolve($name, $context);
        $classReflectionName = ltrim($classReflectionName, '\\');

        return $this->reflectionStorage->getClassReflections()[$classReflectionName];
    }
}
