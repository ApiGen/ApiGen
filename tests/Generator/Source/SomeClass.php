<?php declare(strict_types=1);

namespace ApiGen\Tests\Generator\Source;

class SomeClass
{
    const SOME_CONST = 1;

    public const PUBLIC_CONST = 2;

    /**
     * @deprecated
     */
    public const DEPRECATED_CONST = 5;

    protected const PROTECTED_CONST = 3;

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
