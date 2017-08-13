<?php declare(strict_types=1);

namespace ApiGen\Reflection\Tests\Reflection\Class_\ClassReflection\Source;

/**
 * Huge and small.
 *
 * description.
 *
 * @author Me.
 */
class SomeClass extends ParentClass
{
    /**
     * @var int
     */
    public $someProperty;

    public function someMethod(): void
    {
    }
}
