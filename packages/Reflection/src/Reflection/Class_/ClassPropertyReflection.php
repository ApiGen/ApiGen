<?php declare(strict_types=1);

namespace ApiGen\Reflection\Reflection\Class_;

use ApiGen\Annotation\AnnotationList;
use ApiGen\Reflection\Contract\Reflection\Class_\ClassPropertyReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Class_\ClassReflectionInterface;
use ApiGen\Reflection\Contract\TransformerCollectorAwareInterface;
use ApiGen\Reflection\TransformerCollector;
use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\Types\Object_;
use Roave\BetterReflection\Reflection\ReflectionProperty;

final class ClassPropertyReflection implements ClassPropertyReflectionInterface, TransformerCollectorAwareInterface
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
     * @var TransformerCollector
     */
    private $transformerCollector;

    public function __construct(
        ReflectionProperty $betterPropertyReflection,
        DocBlock $docBlock
    ) {
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
        $description = $this->docBlock->getSummary()
            . AnnotationList::EMPTY_LINE
            . $this->docBlock->getDescription();

        return trim($description);
    }

    public function getStartLine(): int
    {
        return $this->betterPropertyReflection->getStartLine();
    }

    public function getEndLine(): int
    {
        return $this->betterPropertyReflection->getEndLine();
    }

    public function getName(): string
    {
        return $this->betterPropertyReflection->getName();
    }

    public function isDefault(): bool
    {
        return $this->betterPropertyReflection->isDefault();
    }

    public function isStatic(): bool
    {
        return $this->betterPropertyReflection->isStatic();
    }

    /**
     * @return mixed|null
     */
    public function getDefaultValue()
    {
        return $this->betterPropertyReflection->getDefaultValue();
    }

    public function getTypeHint(): string
    {
        $typeHints = $this->betterPropertyReflection->getDocBlockTypes();
        if (! count($typeHints)) {
            return '';
        }

        $typeHint = $typeHints[0];
        if ($typeHint instanceof Object_) {
            $classOrInterfaceName = (string) $typeHint->getFqsen();

            return ltrim($classOrInterfaceName, '\\');
        }

        return implode('|', $this->betterPropertyReflection->getDocBlockTypeStrings());
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

    public function getDeclaringClass(): ClassReflectionInterface
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

    public function isDeprecated(): bool
    {
        if ($this->getDeclaringClass()->isDeprecated()) {
            return true;
        }

        return $this->hasAnnotation(AnnotationList::DEPRECATED);
    }

    public function setTransformerCollector(TransformerCollector $transformerCollector): void
    {
        $this->transformerCollector = $transformerCollector;
    }
}
