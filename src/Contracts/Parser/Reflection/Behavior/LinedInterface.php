<?php declare(strict_types=1);

namespace ApiGen\Contracts\Parser\Reflection\Behavior;

interface LinedInterface
{

    public function getStartLine(): int;


    public function getEndLine(): int;
}
