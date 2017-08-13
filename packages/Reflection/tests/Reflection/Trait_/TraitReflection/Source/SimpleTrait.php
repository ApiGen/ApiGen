<?php declare(strict_types=1);

namespace ApiGen\Reflection\Tests\Reflection\Trait_\TraitReflection\Source;

trait SimpleTrait
{
    use ToBeAliasedTrait {
        ToBeAliasedTrait::aliasedParentMethod as renamedMethod;
    }

    /**
     * @var int
     */
    public $someProperty;

    public function someMethod(): void
    {
    }
}
