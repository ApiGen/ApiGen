<?php declare(strict_types=1);

namespace ApiGen\Reflection\Tests\Reflection\Trait_\TraitReflection\Source;

trait ToBeAliasedTrait
{
    /**
     * @var int
     */
    public $aliasedProperty;

    public function aliasedParentMethod(): void
    {
    }
}
