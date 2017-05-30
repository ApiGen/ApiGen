<?php declare(strict_types=1);

namespace ApiGen\Annotation\AnnotationSubscriber;

use ApiGen\Contracts\Annotation\AnnotationSubscriberInterface;
use ApiGen\Reflection\Contract\Reflection\AbstractReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Class_\AbstractClassElementInterface;
use ApiGen\Reflection\Contract\Reflection\Class_\ClassReflectionInterface;
use ApiGen\StringRouting\Route\ReflectionRoute;
use ApiGen\Templating\Filters\Helpers\LinkBuilder;
use phpDocumentor\Reflection\DocBlock\Tags\Param;
use phpDocumentor\Reflection\Types\Compound;
use phpDocumentor\Reflection\Types\Self_;
use PhpParser\Node\Stmt\Static_;
use phpDocumentor\Reflection\Types\This;

final class ParamAnnotationSubscriber implements AnnotationSubscriberInterface
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

    public function matches($content): bool
    {
        return $content instanceof Param;
    }

    /**
     * @param Param $content
     * @return string
     */
    public function process($content, AbstractReflectionInterface $reflection): string
    {
        if ($content->getType() instanceof Compound) {
            /** @var Compound $compoundType */
            $compoundType = $content->getType();
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

        // @todo

        return '';
    }

    private function resolveSelfThisOrStatic(AbstractReflectionInterface $reflection): ClassReflectionInterface
    {
        if ($reflection instanceof AbstractClassElementInterface) {
            return $reflection->getDeclaringClass();
        }

        // @todo
    }
}

