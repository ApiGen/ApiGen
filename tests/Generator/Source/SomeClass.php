<?php declare(strict_types=1);

namespace ApiGen\Tests\Generator\Source;

class SomeClass
{
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
