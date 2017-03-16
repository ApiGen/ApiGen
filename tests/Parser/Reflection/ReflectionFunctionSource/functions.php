<?php declare(strict_types=1);

/**
 * @param array $arg
 * @return string
 */
function getSomeData(array $arg): string
{
    return $arg[0];
}


/**
 * @param int $first first magic parameter
 * @param string $second first magic parameter
 * @param not propper annotation
 */
function withMagicParameters(): void
{
}


function getMemoryInBytes(string $value)
{
}
