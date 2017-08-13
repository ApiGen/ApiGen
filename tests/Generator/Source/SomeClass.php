<?php declare(strict_types=1);

namespace ApiGen\Tests\Generator\Source;

class SomeClass
{
    /**
     * @var int
     */
    const SOME_CONST = 1;

    /**
     * @var int
     */
    public const PUBLIC_CONST = 2;

    /**
     * @deprecated
     * @var int
     */
    public const DEPRECATED_CONST = 5;

    /**
     * @var int
     */
    protected const PROTECTED_CONST = 3;

    /**
     * @var int
     */
    private const PRIVATE_CONST = 4;

    /**
     * @var string
     */
    public $stringProperty = 'string';

    /**
     * @var string[]
     */
    public $arrayProperty = ['cat', 'dog'];

    /**
     * @var int
     */
    public $integerProperty = 11;

    /**
     * Do not add param annotations here!
     */
    public function functionWithoutParamAnnotations($paramWithoutTypeHint): void
    {
    }
}
