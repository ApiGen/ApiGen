<?php declare(strict_types=1);

namespace ApiGen\Tests\Generator\Source;

trait SomeTrait
{
    /**
     * @var string
     */
    public $property;

    /**
     * Do not add param annotations here!
     */
    public function functionWithoutParamAnnotations($paramWithoutTypeHint): void
    {
    }
}
