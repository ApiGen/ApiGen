<?php declare(strict_types=1);

namespace ApiGen\Reflection\Tests\Parser\ExtendingSources;

use ApiGen\Reflection\Tests\Parser\NotLoadedSources\SomeClass;
use ApiGen\Reflection\Tests\Parser\NotLoadedSources\SomeTrait;

class ExtendingClass extends SomeClass
{
    // use SomeTrait;
}
