<?php declare(strict_types=1);

namespace ApiGen\Tests\Parser\Reflection\ReflectionClassSource;

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
