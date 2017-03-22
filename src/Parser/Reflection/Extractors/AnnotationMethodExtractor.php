<?php declare(strict_types=1);

namespace ApiGen\Parser\Reflection\Extractors;

use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\Extractors\AnnotationMethodExtractorInterface;
use ApiGen\Parser\Reflection\ReflectionClass;
use ApiGen\Parser\Reflection\ReflectionMethodMagic;
use ApiGen\Parser\Reflection\TokenReflection\ReflectionFactory;

final class AnnotationMethodExtractor implements AnnotationMethodExtractorInterface
{
    /**
     * @var string
     */
    public const PATTERN_METHOD = /** @lang RegExp */ '~^
        # static mark
        (?:(static)\\s+)?
        # return typehint
        (?:
            (
                # $this or another word
                (?:\\$this|[\\w\\\\]+)
                # type is array?
                (?:\\[\\])?
                # and again, if "|" is found 
                (?:\\|
                    (?:\\$this|[\\w\\\\])+(?:\\[\\])?
                )*
            )\\s+
        )?
        # return reference?
        (&)?
        \\s*
        # method name
        (\\w+)
        \\s*
        \\(
            # list of arguments
            \\s*
            (
                # argument begin
                (?:
                    # argument typehint
                    (?:(?:[\\w\\\\]+(?:\\[\\])?(?:\\|[\\w\\\\]+(?:\\[\\])?)*)\\s+)?
                    # pass by reference?
                    &?
                    \\s*
                    # argument name
                    \\$\\w+
                    # default value
                    (?:\\s*=\\s*.*)?
                    # optional comma
                    ,?
                    # optional space between comma and next argument
                    \\s*
                )*
            )?    
            \\s*
        \\)                                              
        \\s*
        # description
        (.*|$)
        ~sx';
    const PATTERN_PARAMETER = /** @lang RegExp */  '~^
        # argument typehint
        (?:([\\w\\\\]+(?:\\[\\])?(?:\\|[\\w\\\\]+(?:\\[\\])?)*)\\s+)?
        # pass by reference?
        (&)?
        \\s*
        # argument name
        \\$(\\w+)
        # default value
        (?:\\s*=\\s*(.*))?($)
        ~sx';

    /**
     * @var ReflectionFactory
     */
    private $reflectionFactory;

    /**
     * @var ReflectionClass
     */
    private $reflectionClass;


    public function __construct(ReflectionFactory $reflectionFactory)
    {
        $this->reflectionFactory = $reflectionFactory;
    }


    /**
     * @return ReflectionMethodMagic[]
     */
    public function extractFromReflection(ClassReflectionInterface $reflectionClass): array
    {
        $this->reflectionClass = $reflectionClass;

        $methods = [];
        if ($reflectionClass->hasAnnotation('method')) {
            foreach ($reflectionClass->getAnnotation('method') as $annotation) {
                $methods += $this->processMagicMethodAnnotation($annotation);
            }
        }

        return $methods;
    }


    /**
     * @param string $annotation
     * @return ReflectionMethodMagic[]|array
     */
    private function processMagicMethodAnnotation(string $annotation): array
    {
        if (! preg_match(self::PATTERN_METHOD, $annotation, $matches)) {
            return [];
        }

        [, $static, $returnTypeHint, $returnsReference, $name, $args, $shortDescription] = $matches;

        $startLine = $this->getStartLine($annotation);
        $endLine = $startLine + substr_count($annotation, "\n");

        $methods = [];
        $methods[$name] = $method = $this->reflectionFactory->createMethodMagic([
            'name' => $name,
            'shortDescription' => str_replace("\n", ' ', $shortDescription),
            'startLine' => $startLine,
            'endLine' => $endLine,
            'returnsReference' => ($returnsReference === '&'),
            'declaringClass' => $this->reflectionClass,
            'annotations' => ['return' => [0 => $returnTypeHint]],
            'static' => ($static === 'static')
        ]);
        $this->attachMethodParameters($method, $args);
        return $methods;
    }


    private function getStartLine(string $annotation): int
    {
        $doc = $this->reflectionClass->getDocComment();
        $tmp = $annotation;
        if ($delimiter = strpos($annotation, "\n")) {
            $tmp = substr($annotation, 0, $delimiter);
        }

        return $this->reflectionClass->getStartLine() + substr_count(substr($doc, 0, strpos($doc, $tmp)), "\n");
    }


    private function attachMethodParameters(ReflectionMethodMagic $method, string $args): void
    {
        $parameters = [];
        foreach (array_filter(preg_split('~\\s*,\\s*~', $args)) as $position => $arg) {
            if (! preg_match(self::PATTERN_PARAMETER, $arg, $matches)) {
                // Wrong annotation format
                continue;
            }

            [, $typeHint, $passedByReference, $name, $defaultValueDefinition] = $matches;

            $parameters[$name] = $this->reflectionFactory->createParameterMagic([
                'name' => $name,
                'position' => $position,
                'typeHint' => $typeHint,
                'defaultValueDefinition' => $defaultValueDefinition,
                'unlimited' => false,
                'passedByReference' => ($passedByReference === '&'),
                'declaringFunction' => $method
            ]);
            $method->addAnnotation('param', ltrim(sprintf('%s $%s', $typeHint, $name)));
        }

        $method->setParameters($parameters);
    }
}
