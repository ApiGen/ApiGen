<?php declare(strict_types=1);

namespace ApiGen\Tests\Generator\Source;

function someFunction(): void
{
}

/**
 * Do not add param annotations here!
 */
function someOtherFunction($paramWithoutTypeHint): void
{
}
