<?php declare(strict_types=1);

namespace ApiGen\Tests\Annotation\AnnotationDecoratorSource;

final class SomeClassWithReturnTypes
{
    /**
     * @param int|string[] $value1
     * @param string|$this $value2
     *
     * @see ReturnedClass::$someProperty
     *
     * @return ReturnedClass[]
     */
    public function returnArray(): array
    {
    }

    /**
     * @return ReturnedClass
     */
    public function returnClass()
    {
    }
}
