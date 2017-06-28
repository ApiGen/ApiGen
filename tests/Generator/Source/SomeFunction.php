<?php declare(strict_types=1);

namespace ApiGen\Tests\Generator\Source;

function someFunction(): void
{
}

/**
 * Do not add param annotations here!
 * @return void
 */
function someOtherFunction($paramWithoutTypeHint)
{
}
