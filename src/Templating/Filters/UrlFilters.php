<?php

namespace ApiGen\Templating\Filters;

use ApiGen\Configuration\Configuration;
use ApiGen\Configuration\ConfigurationOptions as CO;
use ApiGen\Contracts\Generator\Resolvers\ElementResolverInterface;
use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ElementReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\FunctionReflectionInterface;
use ApiGen\Generator\SourceCodeHighlighter\SourceCodeHighlighter;
use ApiGen\Templating\Filters\Helpers\ElementLinkFactory;
use ApiGen\Templating\Filters\Helpers\LinkBuilder;
use ApiGen\Templating\Filters\Helpers\Strings;
use Latte\Runtime\Filters as LatteFilters;
use Nette\Utils\Validators;

class UrlFilters extends Filters
{

    /**
     * @var SourceCodeHighlighter
     */
    private $highlighter;

    /**
     * @var ElementResolverInterface
     */
    private $elementResolver;

    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var LinkBuilder
     */
    private $linkBuilder;

    /**
     * @var ElementLinkFactory
     */
    private $elementLinkFactory;


    public function __construct(
        Configuration $configuration,
        SourceCodeHighlighter $highlighter,
        ElementResolverInterface $elementResolver,
        LinkBuilder $linkBuilder,
        ElementLinkFactory $elementLinkFactory
    ) {
        $this->highlighter = $highlighter;
        $this->elementResolver = $elementResolver;
        $this->configuration = $configuration;
        $this->linkBuilder = $linkBuilder;
        $this->elementLinkFactory = $elementLinkFactory;
    }


    /**
     * Tries to parse a definition of a class/method/property/constant/function
     * and returns the appropriate link if successful.
     *
     * @param string $definition
     * @param ElementReflectionInterface $reflectionElement
     * @return string|NULL
     */
    public function resolveLink($definition, ElementReflectionInterface $reflectionElement)
    {
        if (empty($definition)) {
            return null;
        }

        $suffix = '';
        if (substr($definition, -2) === '[]') {
            $definition = substr($definition, 0, -2);
            $suffix = '[]';
        }

        $element = $this->elementResolver->resolveElement($definition, $reflectionElement, $expectedName);
        if ($element === null || $element instanceof FunctionReflectionInterface) {
            return $expectedName;
        }

        $classes = [];
        if ($element->isDeprecated()) {
            $classes[] = 'deprecated';
        }

        /** @var FunctionReflectionInterface $element */
        if (! $element->isValid()) {
            $classes[] = 'invalid';
        }

        $link = $this->createLinkForElement($element, $classes);
        return '<code>' . $link . $suffix . '</code>';
    }


    /**
     * @param string $value
     * @param string $name
     * @param ElementReflectionInterface $reflectionElement
     * @return string
     */
    public function annotation($value, $name, ElementReflectionInterface $reflectionElement)
    {
        $annotationProcessors = [
            'return' => $this->processReturnAnnotations($value, $reflectionElement),
            'throws' => $this->processThrowsAnnotations($value, $reflectionElement),
            'license' => $this->processLicenseAnnotations($value),
            'link' => $this->processLinkAnnotations($value),
            'see' => $this->processSeeAnnotations($value, $reflectionElement),
            'uses' => $this->processUsesAndUsedbyAnnotations($value, $reflectionElement),
            'usedby' => $this->processUsesAndUsedbyAnnotations($value, $reflectionElement),
        ];

        if (isset($annotationProcessors[$name])) {
            return $annotationProcessors[$name];
        }

        return $this->doc($value, $reflectionElement);
    }


    /**
     * Returns links for types.
     *
     * @param string $annotation
     * @param ElementReflectionInterface $reflectionElement
     * @return string
     */
    public function typeLinks($annotation, ElementReflectionInterface $reflectionElement)
    {
        $links = [];

        // typehints can not contains spaces
        // valid typehint is:
        // [TYPE[|TYPE[|...]][SPACE[METHOD|PARAM][DESCRIPTION]]
        $parts = explode(' ', $annotation);

        foreach (explode('|', $parts[0]) as $type) {
            $type = $this->getTypeName($type, false);
            $links[] = $this->resolveLink($type, $reflectionElement) ?: LatteFilters::escapeHtml(ltrim($type, '\\'));
        }

        return implode('|', $links);
    }


    /********************* description *********************/


    /**
     * @param string $annotation
     * @param ElementReflectionInterface $reflectionElement
     * @return string
     */
    public function description($annotation, ElementReflectionInterface $reflectionElement)
    {
        $description = trim(strpbrk($annotation, "\n\r\t $")) ?: $annotation;
        return $this->doc($description, $reflectionElement);
    }


    /**
     * @param ElementReflectionInterface $reflectionElement
     * @return string
     */
    public function shortDescription(ElementReflectionInterface $reflectionElement)
    {
        return $this->doc($reflectionElement->getShortDescription(), $reflectionElement);
    }


    /**
     * @return string
     */
    public function longDescription(ElementReflectionInterface $element)
    {
        $long = $element->getLongDescription();

        // Merge lines
        $long = preg_replace_callback('~(?:<(code|pre)>.+?</\1>)|([^<]*)~s', function ($matches) {
            return ! empty($matches[2])
                ? preg_replace('~\n(?:(\s+\n){2,})+~', ' ', $matches[2])
                : $matches[0];
        }, $long);

        return $this->doc($long, $element, true);
    }


    /********************* text formatter *********************/


    /**
     * @param string $text
     * @param ElementReflectionInterface $reflectionElement
     * @return string
     */
    public function doc($text, ElementReflectionInterface $reflectionElement)
    {
        $text = $this->resolveInternalAnnotation($text);

        return $this->resolveLinkAndSeeAnnotation($text, $reflectionElement);
    }


    /**
     * @param string $text
     * @return string
     */
    private function resolveInternalAnnotation($text)
    {
        $pattern = '~\\{@(\\w+)(?:(?:\\s+((?>(?R)|[^{}]+)*)\\})|\\})~';
        return preg_replace_callback($pattern, function ($matches) {
            if ($matches[1] !== 'internal') {
                return $matches[0];
            }

            if ($this->configuration->getOption(CO::INTERNAL) && isset($matches[2])) {
                return $matches[2];
            }

            return '';
        }, $text);
    }


    /**
     * @param string $text
     * @param ElementReflectionInterface $reflectionElement
     * @return string
     */
    private function resolveLinkAndSeeAnnotation($text, ElementReflectionInterface $reflectionElement)
    {
        return preg_replace_callback('~{@(?:link|see)\\s+([^}]+)}~', function ($matches) use ($reflectionElement) {
            list($url, $description) = Strings::split($matches[1]);

            if ($link = $this->resolveLink($matches[1], $reflectionElement)) {
                return $link;
            }

            if (Validators::isUri($url)) {
                return $this->linkBuilder->build($url, $description ?: $url);
            }

            return $matches[1];
        }, $text);
    }


    /********************* highlight *********************/


    /**
     * @param string $source
     * @param ElementReflectionInterface $reflectionElement
     * @return string
     */
    public function highlightPhp($source, ElementReflectionInterface $reflectionElement)
    {
        return $this->resolveLink($this->getTypeName($source), $reflectionElement)
            ?: $this->highlighter->highlight((string) $source);
    }


    /**
     * @param string $definition
     * @param ElementReflectionInterface $reflectionElement
     * @return string
     */
    public function highlightValue($definition, ElementReflectionInterface $reflectionElement)
    {
        return $this->highlightPhp(preg_replace('~^(?:[ ]{4}|\t)~m', '', $definition), $reflectionElement);
    }


    /**
     * @return string
     */
    private function createLinkForElement($reflectionElement, array $classes)
    {
        return $this->elementLinkFactory->createForElement($reflectionElement, $classes);
    }


    /**
     * @param string $value
     * @param ElementReflectionInterface $reflectionElement
     * @return string
     */
    private function processReturnAnnotations($value, ElementReflectionInterface $reflectionElement)
    {
        $description = $this->getDescriptionFromValue($value, $reflectionElement);
        $typeLinks = $this->typeLinks($value, $reflectionElement);
        return $typeLinks . $description;
    }


    /**
     * @param string $value
     * @param ElementReflectionInterface $elementReflection
     * @return string
     */
    private function processThrowsAnnotations($value, ElementReflectionInterface $elementReflection)
    {
        $description = $this->getDescriptionFromValue($value, $elementReflection);
        $typeLinks = $this->typeLinks($value, $elementReflection);
        return $typeLinks . $description;
    }


    /**
     * @param mixed $value
     * @param ElementReflectionInterface $elementReflection
     * @return string
     */
    private function getDescriptionFromValue($value, ElementReflectionInterface $elementReflection)
    {
        $description = trim(strpbrk($value, "\n\r\t $")) ?: null;
        if ($description) {
            $description = '<br>' . $this->doc($description, $elementReflection);
        }
        return $description;
    }


    /**
     * @param string $value
     * @return string
     */
    private function processLicenseAnnotations($value)
    {
        list($url, $description) = Strings::split($value);
        return $this->linkBuilder->build($url, $description ?: $url);
    }


    /**
     * @param string $value
     * @return string
     */
    private function processLinkAnnotations($value)
    {
        list($url, $description) = Strings::split($value);
        if (Validators::isUrl($url)) {
            return $this->linkBuilder->build($url, $description ?: $url);
        }
        return null;
    }


    /**
     * @param string $value
     * @param ElementReflectionInterface $reflectionElement
     * @return string
     */
    private function processSeeAnnotations($value, ElementReflectionInterface $reflectionElement)
    {
        $doc = [];
        foreach (preg_split('~\\s*,\\s*~', $value) as $link) {
            if ($this->elementResolver->resolveElement($link, $reflectionElement) !== null) {
                $doc[] = $this->typeLinks($link, $reflectionElement);
            } else {
                $doc[] = $this->doc($link, $reflectionElement);
            }
        }
        return implode(', ', $doc);
    }


    /**
     * @param string $value
     * @param ElementReflectionInterface $reflectionElement
     * @return string
     */
    private function processUsesAndUsedbyAnnotations($value, ElementReflectionInterface $reflectionElement)
    {
        list($link, $description) = Strings::split($value);
        $separator = $reflectionElement instanceof ClassReflectionInterface || ! $description ? ' ' : '<br>';
        if ($this->elementResolver->resolveElement($link, $reflectionElement) !== null) {
            $value = $this->typeLinks($link, $reflectionElement) . $separator . $description;
            return trim($value);
        }
        return null;
    }
}
