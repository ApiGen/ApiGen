<?php declare(strict_types=1);

namespace ApiGen\Tests\Generator\Source;

class SomeClass
{
    const SOME_CONST = 1;

    public const PUBLIC_CONST = 2;

    protected const PROTECTED_CONST = 3;

    private const PRIVATE_CONST = 4;

    /**
     * @deprecated
     */
    public const DEPRECATED_CONST = 5;

    public $stringProperty = 'string';

    public $arrayProperty = ['cat', 'dog'];

    public $integerProperty = 11;
    
    /**
     * Do not add param annotations here!
     * @return void
     */
    public function functionWithoutParamAnnotations($paramWithoutTypeHint)
    {
    }
}
