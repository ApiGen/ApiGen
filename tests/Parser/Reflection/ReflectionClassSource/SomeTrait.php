<?php declare(strict_types=1);

namespace Project;

/**
 * @property int $someTraitProperty
 */
trait SomeTrait
{
    /**
     * @var mixed
     */
    public $publicTraitProperty;

    public function publicTraitMethod()
    {
    }
}
