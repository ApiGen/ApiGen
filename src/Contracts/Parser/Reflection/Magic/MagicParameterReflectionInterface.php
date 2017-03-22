<?php declare(strict_types=1);

namespace ApiGen\Contracts\Parser\Reflection\Magic;

use ApiGen\Contracts\Parser\Reflection\ParameterReflectionInterface;

interface MagicParameterReflectionInterface extends ParameterReflectionInterface
{

    public function getDocComment(): string;


    public function getStartLine(): int;


    public function getEndLine(): int;


    public function getFileName(): string;


    public function isTokenized(): bool;
}
