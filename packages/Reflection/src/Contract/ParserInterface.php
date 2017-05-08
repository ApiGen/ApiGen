<?php declare(strict_types=1);

namespace ApiGen\Reflection\Contract;

interface ParserInterface
{
    /**
     * @param string[] $directories
     */
    public function parseDirectories(array $directories): void;
}
