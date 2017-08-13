<?php declare(strict_types=1);

namespace ApiGen\Element\Tests\ReflectionCollector\Source;

final class PartiallyDeprecatedClass
{
    /**
     * @deprecated
     * @var int
     */
    public const LEVEL = 0;

    /**
     * @deprecated
     * @var int
     */
    protected $work;

    /**
     * @deprecated
     */
    public function daily(): void
    {
    }
}
