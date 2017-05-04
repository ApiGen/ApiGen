<?php declare(strict_types=1);

namespace ApiGen\Reflection\Reflection;

use ApiGen\Annotation\AnnotationList;
use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\PropertyReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\TraitReflectionInterface;
use ApiGen\Reflection\Contract\TransformerCollectorInterface;
use phpDocumentor\Reflection\DocBlock;
use Roave\BetterReflection\Reflection\ReflectionProperty;

final class TraitPropertyReflection implements PropertyReflectionInterface
{
    /**
     * @var ReflectionProperty
     */
    private $betterPropertyReflection;

    /**
     * @var DocBlock
     */
    private $docBlock;

    /**
     * @var TransformerCollectorInterface
     */
    private $transformerCollector;

    public function __construct(
        ReflectionProperty $betterPropertyReflection,
        DocBlock $docBlock,
        TransformerCollectorInterface $transformerCollector
    ) {
        $this->betterPropertyReflection = $betterPropertyReflection;
        $this->docBlock = $docBlock;
        $this->transformerCollector = $transformerCollector;
    }

    public function getPrettyName(): string
    {
        // @todo
        return '';
    }

    public function getShortName(): string
    {
        return $this->getName();
    }

    public function isDocumented(): bool
    {
        // TODO: Implement isDocumented() method.
    }

    public function getNamespaceName(): string
    {
        return $this->betterPropertyReflection->getDeclaringClass()
            ->getNamespaceName();
    }

    /**
     * Returns element namespace name.
     * For internal elements returns "PHP", for elements in global space returns "None".
     */
    public function getPseudoNamespaceName(): string
    {
        // TODO: Implement getPseudoNamespaceName() method.
    }


    public function getDescription(): string
    {
        // TODO: Implement getDescription() method.
    }

    public function getDeclaringTrait(): TraitReflectionInterface
    {
        return $this->transformerCollector->transformSingle(
            $this->betterPropertyReflection->getDeclaringClass()
        );
    }

    public function getDeclaringTraitName(): string
    {
        return $this->getDeclaringTrait()
            ->getName();
    }

    public function getStartLine(): int
    {
        // @todo
        return 5;
    }

    public function getEndLine(): int
    {
        // @todo
        return 5;
    }

    public function getName(): string
    {
        return $this->betterPropertyReflection->getName();
    }

    /**
     * @todo: what does this mean? better naming?
     */
    public function isDefault(): bool
    {
        return $this->betterPropertyReflection->isDefault();
    }

    public function isStatic(): bool
    {
        return $this->betterPropertyReflection->isStatic();
    }

    /**
     * @return mixed
     */
    public function getDefaultValue()
    {
        return $this->betterPropertyReflection->getDefaultValue();
    }

    public function getTypeHint(): string
    {
        $annotations = $this->getAnnotation(AnnotationList::VAR_);

        if ($annotations) {
            [$types] = preg_split('~\s+|$~', $annotations[0], 2);
            if (! empty($types) && $types[0] !== '$') {
                return $types;
            }
        }

        try {
            $type = gettype($this->getDefaultValue());
            if (strtolower($type) !== 'null') {
                return $type;
            }
        } catch (\Exception $exception) {
            return '';
        }
    }

    /**
     * @return mixed[]
     */
    public function getAnnotations(): array
    {
        return $this->docBlock->getTags();
    }

    public function hasAnnotation(string $name): bool
    {
        return $this->docBlock->hasTag($name);
    }

    /**
     * @return mixed[]
     */
    public function getAnnotation(string $name): array
    {
        return $this->docBlock->getTagsByName($name);
    }

    public function isPrivate(): bool
    {
        return $this->betterPropertyReflection->isPrivate();
    }

    public function isProtected(): bool
    {
        return $this->betterPropertyReflection->isProtected();
    }

    public function isPublic(): bool
    {
        return $this->betterPropertyReflection->isPublic();
    }

    public function getDeclaringClass(): ?ClassReflectionInterface
    {
        return $this->transformerCollector->transformSingle(
            $this->betterPropertyReflection->getDeclaringClass()
        );
    }

    public function getDeclaringClassName(): string
    {
        return $this->betterPropertyReflection->getDeclaringClass()
            ->getName();
    }

    /**
     * @todo What is this for?
     */
    public function getDefaultValueDefinition(): string
    {
        // @todo
        return $this->betterPropertyReflection->getDefaultValue();
    }
}
