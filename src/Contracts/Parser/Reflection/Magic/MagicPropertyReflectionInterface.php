<?php declare(strict_types=1);

namespace ApiGen\Contracts\Parser\Reflection\Magic;

use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\PropertyReflectionInterface;

interface MagicPropertyReflectionInterface extends PropertyReflectionInterface
{

    public function isDocumented(): bool;


    public function getShortDescription(): string;


    public function getLongDescription(): string;


    public function getDocComment(): string;


    public function isDeprecated(): bool;


    public function setDeclaringClass(ClassReflectionInterface $declaringClass): void;


    public function isPrivate(): bool;


    public function isProtected(): bool;


    public function isPublic(): bool;


    public function getFileName(): string;


    public function isTokenized(): bool;
}
