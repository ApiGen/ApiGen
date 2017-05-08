<?php declare(strict_types=1);

namespace ApiGen\Templating\Filters\Helpers;

use ApiGen\Annotation\AnnotationList;
use ApiGen\Reflection\Contract\Reflection\ClassReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\ClassConstantReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\ReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\FunctionReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\ClassMethodReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\ClassPropertyReflectionInterface;
use ApiGen\Parser\Reflection\AbstractReflection;
use Nette\Utils\Html;
use UnexpectedValueException;

final class ElementLinkFactory
{
    /**
     * @var ElementUrlFactory
     */
    private $elementUrlFactory;

    /**
     * @var LinkBuilder
     */
    private $linkBuilder;

    public function __construct(ElementUrlFactory $elementUrlFactory, LinkBuilder $linkBuilder)
    {
        $this->elementUrlFactory = $elementUrlFactory;
        $this->linkBuilder = $linkBuilder;
    }

    /**
     * @param mixed[] $classes
     */
    public function createForElement(ReflectionInterface $element, array $classes = []): string
    {
        if ($element instanceof ClassReflectionInterface) {
            return $this->createForClass($element, $classes);
        } elseif ($element instanceof ClassMethodReflectionInterface) {
            return $this->createForMethod($element, $classes);
        } elseif ($element instanceof ClassPropertyReflectionInterface) {
            return $this->createForProperty($element, $classes);
        } elseif ($element instanceof ClassConstantReflectionInterface) {
            return $this->createForConstant($element, $classes);
        } elseif ($element instanceof FunctionReflectionInterface) {
            return $this->createForFunction($element, $classes);
        }

        throw new UnexpectedValueException(sprintf(
            'Descendant of "%s" class expected. Got "%s" class.',
            AbstractReflection::class,
            get_class($element)
        ));
    }

    /**
     * @param mixed[] $classes
     */
    private function createForClass(ClassReflectionInterface $reflectionClass, array $classes): string
    {
        return $this->linkBuilder->build(
            $this->elementUrlFactory->createForClass($reflectionClass),
            $reflectionClass->getName(),
            true,
            $classes
        );
    }

    /**
     * @param mixed[] $classes
     */
    private function createForMethod(ClassMethodReflectionInterface $reflectionMethod, array $classes): string
    {
        return $this->linkBuilder->build(
            $this->elementUrlFactory->createForMethod($reflectionMethod),
            $reflectionMethod->getDeclaringClassName() . '::' . $reflectionMethod->getName() . '()',
            false,
            $classes
        );
    }

    /**
     * @param mixed[] $classes
     */
    private function createForProperty(ClassPropertyReflectionInterface $reflectionProperty, array $classes): string
    {
        $text = $reflectionProperty->getDeclaringClassName() . '::' .
            Html::el(AnnotationList::VAR_)->setText('$' . $reflectionProperty->getName());

        return $this->linkBuilder->build(
            $this->elementUrlFactory->createForProperty($reflectionProperty),
            $text,
            false,
            $classes
        );
    }

    /**
     * @param mixed[] $classes
     */
    private function createForConstant(ClassConstantReflectionInterface $reflectionConstant, array $classes): string
    {
        $url = $this->elementUrlFactory->createForConstant($reflectionConstant);

        $text = $reflectionConstant->getDeclaringClassName() . '::' .
        Html::el('b')->setText($reflectionConstant->getName());

        return $this->linkBuilder->build($url, $text, false, $classes);
    }

    /**
     * @param mixed[] $classes
     */
    private function createForFunction(FunctionReflectionInterface $reflectionFunction, array $classes): string
    {
        return $this->linkBuilder->build(
            $this->elementUrlFactory->createForFunction($reflectionFunction),
            $reflectionFunction->getName() . '()',
            true,
            $classes
        );
    }
}
