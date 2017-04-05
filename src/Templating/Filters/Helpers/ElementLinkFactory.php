<?php declare(strict_types=1);

namespace ApiGen\Templating\Filters\Helpers;

use ApiGen\Annotation\AnnotationList;
use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ConstantReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ElementReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\FunctionReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\MethodReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\PropertyReflectionInterface;
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
     * @param ElementReflectionInterface $element
     * @param mixed[] $classes
     */
    public function createForElement(ElementReflectionInterface $element, array $classes = []): string
    {
        if ($element instanceof ClassReflectionInterface) {
            return $this->createForClass($element, $classes);
        } elseif ($element instanceof MethodReflectionInterface) {
            return $this->createForMethod($element, $classes);
        } elseif ($element instanceof PropertyReflectionInterface) {
            return $this->createForProperty($element, $classes);
        } elseif ($element instanceof ConstantReflectionInterface) {
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
     * @param ClassReflectionInterface $reflectionClass
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
     * @param MethodReflectionInterface $reflectionMethod
     * @param mixed[] $classes
     */
    private function createForMethod(MethodReflectionInterface $reflectionMethod, array $classes): string
    {
        return $this->linkBuilder->build(
            $this->elementUrlFactory->createForMethod($reflectionMethod),
            $reflectionMethod->getDeclaringClassName() . '::' . $reflectionMethod->getName() . '()',
            false,
            $classes
        );
    }

    /**
     * @param PropertyReflectionInterface $reflectionProperty
     * @param mixed[] $classes
     */
    private function createForProperty(PropertyReflectionInterface $reflectionProperty, array $classes): string
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
     * @param ConstantReflectionInterface $reflectionConstant
     * @param mixed[] $classes
     */
    private function createForConstant(ConstantReflectionInterface $reflectionConstant, array $classes): string
    {
        $url = $this->elementUrlFactory->createForConstant($reflectionConstant);

        if ($reflectionConstant->getDeclaringClassName()) {
            $text = $reflectionConstant->getDeclaringClassName() . '::' .
                Html::el('b')->setText($reflectionConstant->getName());
        } else {
            $text = $this->getGlobalConstantName($reflectionConstant);
        }

        return $this->linkBuilder->build($url, $text, false, $classes);
    }

    /**
     * @param FunctionReflectionInterface $reflectionFunction
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

    private function getGlobalConstantName(ConstantReflectionInterface $reflectionConstant): string
    {
        if ($reflectionConstant->getNamespaceName()) {
            return $reflectionConstant->getNamespaceName() . '\\' .
                Html::el('b')->setText($reflectionConstant->getShortName());
        } else {
            return (string) Html::el('b')->setText($reflectionConstant->getName());
        }
    }
}
