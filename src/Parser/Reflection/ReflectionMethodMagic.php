<?php declare(strict_types=1);

namespace ApiGen\Parser\Reflection;

use ApiGen\Contracts\Parser\Reflection\Magic\MagicMethodReflectionInterface;
use ApiGen\Parser\Reflection\Parts\IsDocumentedMagic;
use ApiGen\Parser\Reflection\Parts\StartLineEndLine;
use ApiGen\Parser\Reflection\Parts\StartPositionEndPositionMagic;

class ReflectionMethodMagic extends ReflectionMethod implements MagicMethodReflectionInterface
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
    private $shortDescription;

    /**
     * @var bool
     */
    private $returnsReference;

    /**
     * @var ReflectionClass
     */
    private $declaringClass;

    /**
     * @var bool
     */
    private $static;


    public function __construct(array $settings)
    {
        $this->name = $settings['name'];
        $this->shortDescription = $settings['shortDescription'];
        $this->startLine = $settings['startLine'];
        $this->endLine = $settings['endLine'];
        $this->returnsReference = $settings['returnsReference'];
        $this->declaringClass = $settings['declaringClass'];
        $this->annotations = $settings['annotations'];
        $this->static = isset($settings['static']) ? $settings['static'] : false;

        $this->reflectionType = get_class($this);
    }


    public function getName()
    {
        return $this->name;
    }


    public function getShortDescription()
    {
        return $this->shortDescription;
    }


    public function getLongDescription()
    {
        return $this->shortDescription;
    }


    public function returnsReference(): bool
    {
        return $this->returnsReference;
    }


    public function isMagic(): bool
    {
        return true;
    }


    public function getShortName(): string
    {
        return $this->name;
    }


    public function isDeprecated()
    {
        return $this->declaringClass->isDeprecated();
    }


    public function getNamespaceName()
    {
        return $this->declaringClass->getNamespaceName();
    }


    public function getAnnotations()
    {
        if ($this->annotations === null) {
            $this->annotations = [];
        }
        return $this->annotations;
    }


    public function getDeclaringClass()
    {
        return $this->declaringClass;
    }


    public function getDeclaringClassName(): string
    {
        return $this->declaringClass->getName();
    }


    public function isAbstract(): bool
    {
        return false;
    }


    public function isFinal(): bool
    {
        return false;
    }


    public function isPrivate()
    {
        return false;
    }


    public function isProtected()
    {
        return false;
    }


    public function isPublic()
    {
        return true;
    }


    public function isStatic()
    {
        return $this->static;
    }


    public function getDeclaringTrait()
    {
        return $this->declaringClass->isTrait() ? $this->declaringClass : null;
    }


    public function getDeclaringTraitName()
    {
        if ($declaringTrait = $this->getDeclaringTrait()) {
            return $declaringTrait->getName();
        }
        return null;
    }


    public function getOriginalName()
    {
        return $this->getName();
    }


    public function getParameters()
    {
        return $this->parameters;
    }


    public function setParameters(array $parameters): void
    {
        $this->parameters = $parameters;
    }


    public function getNamespaceAliases(): array
    {
        return $this->declaringClass->getNamespaceAliases();
    }


    public function getPrettyName(): string
    {
        return sprintf('%s::%s()', $this->declaringClass->getName(), $this->name);
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

        if ($annotations = $this->getAnnotation('param')) {
            foreach ($annotations as $annotation) {
                $docComment .= sprintf("@param %s\n", $annotation);
            }
        }

        if ($annotations = $this->getAnnotation('return')) {
            foreach ($annotations as $annotation) {
                $docComment .= sprintf("@return %s\n", $annotation);
            }
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
        return [];
    }
}
