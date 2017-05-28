<?php declare(strict_types=1);

namespace ApiGen\Tests\Annotation\AnnotationSubscriber\SeeAnnotationSubscriberSource;

use ApiGen\Tests\Annotation\AnnotationDecoratorSource\ReturnedClass;

final class SomeClassWithSeeAnnotations
{
    /**
     * @see ReturnedClass::$someProperty
     * @see ReturnedClass::someMethod()
     * @see PresentReturnedClass::$someProperty
     * @see PresentReturnedClass::someMethod()
     */
    public function returnArray(): array
    {
    }
}
