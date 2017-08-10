<?php declare(strict_types=1);

namespace ApiGen\Annotation\Tests\AnnotationSubscriber\SeeAnnotationSubscriberSource;

use ApiGen\Annotation\Tests\AnnotationDecoratorSource\ReturnedClass;

function someExistingFunction(): void
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
