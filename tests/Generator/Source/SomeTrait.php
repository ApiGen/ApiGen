<?php declare(strict_types=1);

namespace ApiGen\Tests\Generator\Source;

trait SomeTrait
{
    public $prop;

    /**
     * Do not add param annotations here!
     * @return void
     */
    public function functionWithoutParamAnnotations($paramWithoutTypeHint)
    {
    }
}
