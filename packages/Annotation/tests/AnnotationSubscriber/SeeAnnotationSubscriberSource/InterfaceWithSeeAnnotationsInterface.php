<?php declare(strict_types=1);

namespace ApiGen\Annotation\Tests\AnnotationSubscriber\SeeAnnotationSubscriberSource;

interface InterfaceWithSeeAnnotationsInterface
{
    /**
     * Test.
     *
     * @see SomeClassWithSeeAnnotations::returnArray()
     */
    public function someSexyMethod(): int;
}
