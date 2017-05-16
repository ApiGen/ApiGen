<?php declare(strict_types=1);

namespace ApiGen\StringRouting\Route;

use ApiGen\StringRouting\Contract\Route\RouteInterface;

final class ReflectionRoute implements RouteInterface
{
    /**
     * @var string
     */
    public const NAME = 'reflection';

    public function match(string $name): bool
    {
        return $name === self::NAME;
    }

    /**
     * @param mixed $argument
     */
    public function constructUrl($argument): string
    {
        // TODO: Implement constructUrl() method.
    }

//    /**
//     * @var ElementUrlFactory
//     */
//    private $elementUrlFactory;
//
//    public function __construct(ElementUrlFactory $elementUrlFactory)
//    {
//        $this->elementUrlFactory = $elementUrlFactory;
//    }
//
//    /**
//     * @return callable[]
//     */
//    public function getFilters(): array
//    {
//        return [
//
//        ];
//    }
//
//    public function elementUrl(ReflectionInterface $element): string
//    {
//        return $this->elementUrlFactory->createForElement($element);
//    }
////    /**
////     * @param string|ClassReflectionInterface $class
////     */
//
//    public function classUrl(ClassReflectionInterface $class): string
//    {
//        return $this->elementUrlFactory->createForClass($class);
//    }
//
//    public function methodUrl(AbstractMethodReflectionInterface $methodReflection) { //, ?ClassReflectionInterface $class = null): string
//        {
//            return $this->elementUrlFactory->createForMethod($methodReflection); //, $class);
//        }
//
//        public function propertyUrl(ClassPropertyReflectionInterface $propertyReflection) { //}, ?ClassReflectionInterface $class = null): string
//            {
//                return $this->elementUrlFactory->createForProperty($propertyReflection);
//                //, $class);
//            }
//
//            public function constantUrl(ClassConstantReflectionInterface $classConstantReflection): string
//            {
//                return $this->elementUrlFactory->createForConstant($classConstantReflection);
//            }
//
//            public function functionUrl(FunctionReflectionInterface $functionReflection): string
//            {
//                return $this->elementUrlFactory->createForFunction($functionReflection);
//            }
}
