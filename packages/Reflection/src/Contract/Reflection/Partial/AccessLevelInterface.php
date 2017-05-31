<?php declare(strict_types=1);

namespace ApiGen\Reflection\Contract\Reflection\Partial;

interface AccessLevelInterface
{
    public function isPublic(): bool;

    public function isProtected(): bool;

    public function isPrivate(): bool;
}
