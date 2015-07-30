<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Parser\Reflection\Extractors;

use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\Extractors\AnnotationPropertyExtractorInterface;
use ApiGen\Contracts\Parser\Reflection\Magic\MagicPropertyReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\TokenReflection\ReflectionFactoryInterface;

class AnnotationPropertyExtractor implements AnnotationPropertyExtractorInterface
{

    const PATTERN_PROPERTY = '~^(?:([\\w\\\\]+(?:\\|[\\w\\\\]+)*)\\s+)?\\$(\\w+)(?:\\s+(.*))?($)~s';

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
     * {@inheritdoc}
     */
    public function extractFromReflection(ClassReflectionInterface $classReflection)
    {
        $this->classReflection = $classReflection;

        $properties = [];
        foreach (['property', 'property-read', 'property-write'] as $annotationName) {
            if ($this->classReflection->hasAnnotation($annotationName)) {
                foreach ($this->classReflection->getAnnotation($annotationName) as $annotation) {
                    $properties += $this->processMagicPropertyAnnotation($annotation, $annotationName);
                };
            }
        }

        return $properties;
    }


    /**
     * @param string $annotation
     * @param string $annotationName
     * @return MagicPropertyReflectionInterface[]
     */
    private function processMagicPropertyAnnotation($annotation, $annotationName)
    {
        if (! preg_match(self::PATTERN_PROPERTY, $annotation, $matches)) {
            return [];
        }

        list(, $typeHint, $name, $shortDescription) = $matches;

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


    /**
     * @param string $annotation
     * @return int
     */
    private function getStartLine($annotation)
    {
        $doc = $this->classReflection->getDocComment();
        $tmp = $annotation;
        if ($delimiter = strpos($annotation, "\n")) {
            $tmp = substr($annotation, 0, $delimiter);
        }
        return $this->classReflection->getStartLine() + substr_count(substr($doc, 0, strpos($doc, $tmp)), "\n");
    }
}
