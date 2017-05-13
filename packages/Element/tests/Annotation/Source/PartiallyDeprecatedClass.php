<?php declare(strict_types=1);

namespace ApiGen\Element\Tests\Annotation\Annotation;

final class PartiallyDeprecatedClass
{
    /**
     * @deprecated
     */
    private const LEVEL = 0;

    /**
     * @deprecated
     */
    protected $work;

    /**
     * @deprecated
     */
    public function daily()
    {

    }
}
