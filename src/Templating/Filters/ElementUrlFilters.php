<?php declare(strict_types=1);

namespace ApiGen\Templating\Filters;

use ApiGen\Reflection\Contract\Reflection\AbstractMethodReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Class_\ClassConstantReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Class_\ClassReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Function_\FunctionReflectionInterface;
use ApiGen\Templating\Filters\Helpers\ElementUrlFactory;
use Symplify\ModularLatteFilters\Contract\DI\LatteFiltersProviderInterface;

final class ElementUrlFilters implements LatteFiltersProviderInterface
{
    /**
     * @var ElementUrlFactory
     */
    private $elementUrlFactory;

    public function __construct(ElementUrlFactory $elementUrlFactory)
    {
        $this->elementUrlFactory = $elementUrlFactory;
    }

    /**
     * @return callable[]
     */
    public function getFilters(): array
    {
        return [

        ];
    }

    public function elementUrl(ReflectionInterface $element): string
    {
        return $this->elementUrlFactory->createForElement($element);
    }
//    /**
//     * @param string|ClassReflectionInterface $class
//     */

    public function classUrl(ClassReflectionInterface $class): string
    {
        return $this->elementUrlFactory->createForClass($class);
    }

    public function methodUrl(AbstractMethodReflectionInterface $methodReflection) { //, ?ClassReflectionInterface $class = null): string
    {
        return $this->elementUrlFactory->createForMethod($methodReflection); //, $class);
    }

    public function propertyUrl(ClassPropertyReflectionInterface $propertyReflection) { //}, ?ClassReflectionInterface $class = null): string
    {
        return $this->elementUrlFactory->createForProperty($propertyReflection);
        //, $class);
    }

    public function constantUrl(ClassConstantReflectionInterface $classConstantReflection): string
    {
        return $this->elementUrlFactory->createForConstant($classConstantReflection);
    }

    public function functionUrl(FunctionReflectionInterface $functionReflection): string
    {
        return $this->elementUrlFactory->createForFunction($functionReflection);
    }
}
