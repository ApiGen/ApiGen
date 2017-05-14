<?php declare(strict_types=1);

namespace ApiGen\Reflection\Tests\Reflection\Class_\ClassReflection\Source;

/**
 * @property int $someTraitProperty
 */
trait SomeTrait
{
    /**
     * @var mixed
     */
    public $publicTraitProperty;

    public function publicTraitMethod(): void
    {
    }
}
