<?php declare(strict_types=1);

namespace ApiGen\Annotation;

use ApiGen\Reflection\Contract\Reflection\AbstractReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Class_\AbstractClassElementInterface;
use ApiGen\Reflection\Contract\Reflection\Class_\ClassMethodReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Class_\ClassReflectionInterface;
use ApiGen\Reflection\Contract\ReflectionStorageInterface;
use ApiGen\StringRouting\Route\ReflectionRoute;
use ApiGen\Templating\Filters\Helpers\LinkBuilder;
use phpDocumentor\Reflection\DocBlock\Tag;
use phpDocumentor\Reflection\DocBlock\Tags\Param;
use phpDocumentor\Reflection\DocBlock\Tags\Return_;
use phpDocumentor\Reflection\FqsenResolver;
use phpDocumentor\Reflection\Types\Array_;
use phpDocumentor\Reflection\Types\Compound;
use phpDocumentor\Reflection\Types\ContextFactory;
use phpDocumentor\Reflection\Types\Object_;
use phpDocumentor\Reflection\Types\Self_;
use phpDocumentor\Reflection\Types\Static_;
use phpDocumentor\Reflection\Types\This;
use ReflectionClass;

# see: https://github.com/phpDocumentor/TypeResolver#resolving-an-fqsen

final class AnnotationDecorator
{
    /**
     * @var ReflectionRoute
     */
    private $reflectionRoute;

    /**
     * @var ReflectionStorageInterface
     */
    private $reflectionStorage;

    /**
     * @var LinkBuilder
     */
    private $linkBuilder;

    public function __construct(
        ReflectionRoute $reflectionRoute,
        ReflectionStorageInterface $reflectionStorage,
        LinkBuilder $linkBuilder
    ) {
        $this->reflectionRoute = $reflectionRoute;
        $this->reflectionStorage = $reflectionStorage;
        $this->linkBuilder = $linkBuilder;
    }

    // @todo set SingleAnnotationDecoratorInterface
    // listen to @return, @see... etc.

    public function decorate(Tag $tag, AbstractReflectionInterface $reflection): string
    {
        if ($tag instanceof Return_) {
            if ($tag->getType() instanceof Array_) {
                /** @var Array_ $arrayType */
                $arrayType = $tag->getType();
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
        } elseif ($tag instanceof Param) {
            if ($tag->getType() instanceof Compound) {
                /** @var Compound $compoundType */
                $compoundType = $tag->getType();
                $returnValue = '';
                $i = 0;
                while ($compoundType->has($i)) {
                    $singleType = $compoundType->get($i);
                    if ($singleType instanceof This || $singleType instanceof Static_ || $singleType instanceof Self_) {
                        $classReflection = $this->resolveSelfThisOrStatic($reflection);
                        $classUrl = $this->reflectionRoute->constructUrl($classReflection);
                        $singleType = '<code>' . $this->linkBuilder->build($classUrl, (string) $singleType) . '</code>';
                    }

                    $returnValue .= $singleType . '|';
                    ++$i;
                }

                return rtrim($returnValue, '|');
            }
        }
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

    private function resolveSelfThisOrStatic(AbstractReflectionInterface $reflection): ClassReflectionInterface
    {
        if ($reflection instanceof AbstractClassElementInterface) {
            return $reflection->getDeclaringClass();
        }
        // @todo
    }
}
