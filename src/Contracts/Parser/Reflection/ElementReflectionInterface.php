<?php declare(strict_types=1);

namespace ApiGen\Contracts\Parser\Reflection;

use ApiGen\Contracts\Parser\Reflection\Behavior\LinedInterface;
use ApiGen\Contracts\Parser\Reflection\Behavior\NamedInterface;
use TokenReflection\Exception\BaseException;

interface ElementReflectionInterface extends NamedInterface
{

    public function isMain(): bool;


    public function isValid(): bool;


    public function isDocumented(): bool;


    public function isDeprecated(): bool;


    public function inNamespace(): bool;


    public function getNamespaceName(): string;


    /**
     * Returns element namespace name.
     * For internal elements returns "PHP", for elements in global space returns "None".
     *
     * @return string
     */
    public function getPseudoNamespaceName(): string;


    /**
     * @return string[]
     */
    public function getNamespaceAliases(): array;


    /**
     * Returns reflection element annotations.
     * Removes the short and long description.
     * In case of classes, functions and constants, @package, @subpackage, @author and @license annotations
     * are added from declaring files if not already present.
     *
     * @return array
     */
    public function getAnnotations(): array;


    /**
     * @param string $name
     * @return array
     */
    public function getAnnotation(string $name): array;


    public function hasAnnotation(string $name): bool;


    /**
     * @param string $name
     * @param mixed $value
     */

    public function addAnnotation(string $name, $value): void;


    public function getShortDescription(): string;


    public function getLongDescription(): string;


    /**
     * @return string|bool
     */
    public function getDocComment();


    public function getPrettyName(): string;


    /**
     * Returns the unqualified name (UQN).
     *
     * @return string
     */
    public function getShortName(): string;


    public function getStartPosition(): int;


    public function getEndPosition(): int;


    public function addReason(BaseException $reason): void;


    /**
     * @return BaseException[]
     */
    public function getReasons(): array;


    public function hasReasons(): bool;
}
