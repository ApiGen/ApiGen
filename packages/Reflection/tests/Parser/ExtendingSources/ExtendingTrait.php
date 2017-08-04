<?php declare(strict_types=1);

namespace ApiGen\Reflection\Tests\Parser\ExtendingSources;

use ApiGen\Reflection\Tests\Parser\NotLoadedSources;

trait ExtendingTrait
{
    use NotLoadedSources\SomeTrait;
}
