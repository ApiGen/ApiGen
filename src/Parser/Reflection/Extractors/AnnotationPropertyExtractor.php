<?php declare(strict_types=1);

namespace ApiGen\Parser\Reflection\Extractors;

use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\Extractors\AnnotationPropertyExtractorInterface;
use ApiGen\Contracts\Parser\Reflection\Magic\MagicPropertyReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\PropertyReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\TokenReflection\ReflectionFactoryInterface;

final class AnnotationPropertyExtractor implements AnnotationPropertyExtractorInterface
{
    /**
     * @var string
     */
    public const PATTERN_PROPERTY = /** @lang RegExp */ '~^
        # property typehint
        (?:
            ([\\w\\\\]+(?:\\[\\])?(?:\\|[\\w\\\\]+(?:\\[\\])?)*)\\s+
        )?
        # property name
        \\$(\\w+)
        # optional property description
        (?:
            \\s+(.*)
        )?
        ($)
        ~sx';

    /**
     * @var ReflectionFactoryInterface
     */
    private $reflectionFactory;

    /**
     * @var ClassReflectionInterface
     */
    private $classReflection;


    public function __construct(ReflectionFactoryInterface $reflectionFactory)
    {
        $this->reflectionFactory = $reflectionFactory;
    }


    /**
     * @param ClassReflectionInterface $classReflection
     * @return PropertyReflectionInterface[]
     */
    public function extractFromReflection(ClassReflectionInterface $classReflection): array
    {
        $this->classReflection = $classReflection;

        $properties = [];
        foreach (['property', 'property-read', 'property-write'] as $annotationName) {
            if ($this->classReflection->hasAnnotation($annotationName)) {
                foreach ($this->classReflection->getAnnotation($annotationName) as $annotation) {
                    $properties += $this->processMagicPropertyAnnotation($annotation, $annotationName);
                }
            }
        }

        return $properties;
    }


    /**
     * @param string $annotation
     * @param string $annotationName
     * @return MagicPropertyReflectionInterface[]
     */
    private function processMagicPropertyAnnotation(
        string $annotation, string $annotationName
    ): array {
        if (! preg_match(self::PATTERN_PROPERTY, $annotation, $matches)) {
            return [];
        }

        [, $typeHint, $name, $shortDescription] = $matches;

        $startLine = $this->getStartLine($annotation);
        $properties = [];
        $properties[$name] = $this->reflectionFactory->createPropertyMagic([
            'name' => $name,
            'typeHint' => $typeHint,
            'shortDescription' => str_replace("\n", ' ', $shortDescription),
            'startLine' => $startLine,
            'endLine' => $startLine + substr_count($annotation, "\n"),
            'readOnly' => ($annotationName === 'property-read'),
            'writeOnly' => ($annotationName === 'property-write'),
            'declaringClass' => $this->classReflection
        ]);
        return $properties;
    }


    private function getStartLine(string $annotation): int
    {
        $doc = $this->classReflection->getDocComment();
        $tmp = $annotation;
        if ($delimiter = strpos($annotation, "\n")) {
            $tmp = substr($annotation, 0, $delimiter);
        }

        return $this->classReflection->getStartLine() + substr_count(substr($doc, 0, strpos($doc, $tmp)), "\n");
    }
}
