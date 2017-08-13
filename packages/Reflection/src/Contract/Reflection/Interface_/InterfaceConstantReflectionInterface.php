<?php declare(strict_types=1);

namespace ApiGen\Reflection\Contract\Reflection\Interface_;

use ApiGen\Reflection\Contract\Reflection\Partial\AnnotationsInterface;

interface InterfaceConstantReflectionInterface extends AbstractInterfaceElementInterface, AnnotationsInterface
{
    /**
     * @return mixed
     */
    public function getValue();

    public function getTypeHint(): string;

    public function isPublic(): bool;

    public function isProtected(): bool;

    public function isPrivate(): bool;
}
