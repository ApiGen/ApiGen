<?php declare(strict_types=1);

namespace Project;

/**
 * @method getSomeTraitMagic()
 * @property int $someTraitProperty
 */
trait SomeTrait
{

    public $publicTraitProperty;


    public function publicTraitMethod(): void
    {
    }
}
