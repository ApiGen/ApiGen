<?php declare(strict_types=1);

namespace ApiGen\Reflection\Tests\Reflection\Trait_\TraitReflection\ComposeTraitSource;

class BaseClass
{
    use FirstTrait;
    use SecondTrait;

    public function hey(): void
    {
        echo 'Hi';
    }
}
