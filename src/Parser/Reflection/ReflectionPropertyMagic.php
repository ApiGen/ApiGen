<?php declare(strict_types=1);

namespace ApiGen\Parser\Reflection;

use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\Magic\MagicPropertyReflectionInterface;
use ApiGen\Parser\Reflection\Parts\IsDocumentedMagic;
use ApiGen\Parser\Reflection\Parts\StartLineEndLine;
use ApiGen\Parser\Reflection\Parts\StartPositionEndPositionMagic;

/**
 * Envelope for magic properties that are defined
 * only as @property, @property-read or @property-write annotation.
 */
class ReflectionPropertyMagic extends ReflectionProperty implements MagicPropertyReflectionInterface
{

    use IsDocumentedMagic;
    use StartLineEndLine;
    use StartPositionEndPositionMagic;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $typeHint;

    /**
     * @var string
     */
    private $shortDescription;

    /**
     * @var string
     */
    private $longDescription;

    /**
     * @var bool
     */
    private $readOnly;

    /**
     * @var bool
     */
    private $writeOnly;

    /**
     * @var ReflectionClass
     */
    private $declaringClass;


    public function __construct(array $options)
    {
        $this->name = $options['name'];
        $this->typeHint = $options['typeHint'];
        $this->shortDescription = $options['shortDescription'];
        $this->startLine = $options['startLine'];
        $this->endLine = $options['endLine'];
        $this->readOnly = $options['readOnly'];
        $this->writeOnly = $options['writeOnly'];
        $this->declaringClass = $options['declaringClass'];
        $this->addAnnotation('var', $options['typeHint']);
        $this->reflectionType = get_class($this);
    }


    public function getName(): string
    {
        return $this->name;
    }


    public function getTypeHint(): string
    {
        return $this->typeHint;
    }


    public function isWriteOnly(): bool
    {
        return $this->writeOnly;
    }


    public function getShortDescription(): string
    {
        return $this->shortDescription;
    }


    public function getLongDescription(): string
    {
        return $this->longDescription;
    }


    public function isReadOnly():bool
    {
        return $this->readOnly;
    }


    public function isMagic(): bool
    {
        return true;
    }


    public function isDeprecated(): bool
    {
        return $this->declaringClass->isDeprecated();
    }


    public function getNamespaceName(): string
    {
        return $this->declaringClass->getNamespaceName();
    }


    public function getAnnotations(): array
    {
        if ($this->annotations === null) {
            $this->annotations = [];
        }

        return $this->annotations;
    }


    public function getDeclaringClass(): ?ClassReflectionInterface
    {
        return $this->declaringClass;
    }


    public function getDeclaringClassName(): string
    {
        return $this->declaringClass->getName();
    }


    public function setDeclaringClass(ClassReflectionInterface $declaringClass): void
    {
        $this->declaringClass = $declaringClass;
    }


    public function getDefaultValue()
    {
        return null;
    }


    public function isDefault(): bool
    {
        return false;
    }


    public function isPrivate(): bool
    {
        return false;
    }


    public function isProtected(): bool
    {
        return false;
    }


    public function isPublic(): bool
    {
        return true;
    }


    public function isStatic(): bool
    {
        return false;
    }


    public function getDeclaringTrait(): ?ClassReflectionInterface
    {
        return $this->declaringClass->isTrait() ? $this->declaringClass : null;
    }


    public function getDeclaringTraitName(): string
    {
        if ($declaringTrait = $this->getDeclaringTrait()) {
            return $declaringTrait->getName();
        }
        return null;
    }


    public function getNamespaceAliases(): array
    {
        return $this->declaringClass->getNamespaceAliases();
    }


    public function getPrettyName(): string
    {
        return sprintf(
            '%s::$%s',
            $this->declaringClass->getName(),
            $this->name
        );
    }


    public function getFileName(): string
    {
        return $this->declaringClass->getFileName();
    }


    public function isTokenized(): bool
    {
        return true;
    }


    public function getDocComment(): string
    {
        $docComment = "/**\n";

        if (! empty($this->shortDescription)) {
            $docComment .= $this->shortDescription . "\n\n";
        }

        if ($annotations = $this->getAnnotation('var')) {
            $docComment .= sprintf("@var %s\n", $annotations[0]);
        }

        $docComment .= "*/\n";

        return $docComment;
    }


    public function hasAnnotation(string $name): bool
    {
        $annotations = $this->getAnnotations();
        return array_key_exists($name, $annotations);
    }


    public function getAnnotation(string $name): array
    {
        $annotations = $this->getAnnotations();
        if (array_key_exists($name, $annotations)) {
            return $annotations[$name];
        }
        return null;
    }
}
