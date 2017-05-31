<?php declare(strict_types=1);

namespace ApiGen\Reflection\Contract\Reflection\Partial;

interface StartAndEndLineInterface
{
    public function getStartLine(): int;

    public function getEndLine(): int;
}
