<?php declare(strict_types=1);

namespace ApiGen\Annotation\Tests\AnnotationSubscriber\SeeAnnotationSubscriberSource;

use ApiGen\Annotation\Tests\AnnotationDecoratorSource\ReturnedClass;

interface SomeInterfaceWithSeeAnnotations
{
    /**
     * @see SomeClassWithSeeAnnotations::returnArray()
     */
    public function someSexyMethod(): int;
}
