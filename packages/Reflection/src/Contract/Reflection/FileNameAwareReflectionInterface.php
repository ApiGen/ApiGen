<?php declare(strict_types=1);

namespace ApiGen\Reflection\Contract\Reflection;

interface FileNameAwareReflectionInterface
{
    /**
     * @return string|null  returns null for PHP internal classes
     */
    public function getFileName(): ?string;
}
