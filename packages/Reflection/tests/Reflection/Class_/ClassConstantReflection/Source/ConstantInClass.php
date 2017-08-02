<?php declare(strict_types=1);

namespace ApiGen\Reflection\Tests\Reflection\Class_\ClassConstantReflection\Source;

class ConstantInClass
{
    /**
     * Nice description.
     *
     * @var int
     */
    public const CONSTANT_INSIDE = 55;

    /**
     * @var string
     */
    protected const COMPOSED = 'right' . ' now';

    /**
     * @var string[]
     */
    protected const ARRAY_CONSTANT = [1, 2];

    /**
     * @var string
     */
    protected const COMPOSED_WITH_DIR = __DIR__ . '/here';
}
