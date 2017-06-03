<?php declare(strict_types=1);

namespace ApiGen\Annotation\Tests\AnnotationSubscriber\ResolvePropertySource;

final class SelfPropertyClass
{
    /**
     * @var string
     */
    public $someProperty;

    /**
     * @see SelfPropertyClass::$someProperty
     * # broken @see $someProperty
     */
    public function someMethod(): void
    {
    }
}
