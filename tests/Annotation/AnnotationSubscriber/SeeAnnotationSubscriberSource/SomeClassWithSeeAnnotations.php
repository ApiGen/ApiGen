<?php declare(strict_types=1);

namespace ApiGen\Tests\Annotation\AnnotationSubscriber\SeeAnnotationSubscriberSource;

use ApiGen\Tests\Annotation\AnnotationDecoratorSource\ReturnedClass;

function someExistingFunction()
{

}

final class SomeClassWithSeeAnnotations
{
    /**
     * @see ReturnedClass::$someProperty
     * @see ReturnedClass::someMethod()
     *
     * @see PresentReturnedClass::$someProperty
     * @see PresentReturnedClass::someMethod()
     *
     * @see someMissingFunction()
     * @see someExistingFunction()
     */
    public function returnArray(): array
    {
    }
}
