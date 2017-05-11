<?php declare(strict_types=1);

namespace ApiGen\Reflection\Reflection\Trait_;

use ApiGen\Annotation\AnnotationList;
use ApiGen\Reflection\Contract\Reflection\Trait_\TraitPropertyReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Trait_\TraitReflectionInterface;
use ApiGen\Reflection\Contract\TransformerCollectorInterface;
use phpDocumentor\Reflection\DocBlock;
use Roave\BetterReflection\Reflection\ReflectionProperty;

final class TraitPropertyReflection implements TraitPropertyReflectionInterface
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

    public function __construct(ReflectionProperty $betterPropertyReflection, DocBlock $docBlock)
    {
        $this->betterPropertyReflection = $betterPropertyReflection;
        $this->docBlock = $docBlock;
    }

    public function getNamespaceName(): string
    {
        return $this->betterPropertyReflection->getDeclaringClass()
            ->getNamespaceName();
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

//        if ($annotations) {
//            [$types] = preg_split('~\s+|$~', $annotations[0], 2);
//            if (! empty($types) && $types[0] !== '$') {
//                return $types;
//            }
//        }

//        try {
//            $type = gettype($this->getDefaultValue());
//            if (strtolower($type) !== 'null') {
//                return $type;
//            }
//        } catch (\Exception $exception) {
//            return '';
//        }
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

    public function isDeprecated(): bool
    {
        return $this->hasAnnotation(AnnotationList::DEPRECATED);
    }
}
