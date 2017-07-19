<?php declare(strict_types=1);

namespace ApiGen\Reflection\Contract\Reflection;

interface FileNameAwareReflectionInterface
{
    public function getFileName(): ?string;
}
