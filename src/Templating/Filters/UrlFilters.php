<?php declare(strict_types=1);

namespace ApiGen\Templating\Filters;

use ApiGen\Contracts\Generator\Resolvers\ElementResolverInterface;
use ApiGen\Contracts\Generator\SourceCodeHighlighter\SourceCodeHighlighterInterface;
use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ElementReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\FunctionReflectionInterface;
use ApiGen\Event\ProcessDocTextEvent;
use ApiGen\Templating\Filters\Helpers\ElementLinkFactory;
use ApiGen\Templating\Filters\Helpers\LinkBuilder;
use ApiGen\Templating\Filters\Helpers\Strings;
use Latte\Runtime\Filters as LatteFilters;
use Nette\Utils\Validators;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class UrlFilters extends Filters
{
    /**
     * @var SourceCodeHighlighterInterface
     */
    private $highlighter;

    /**
     * @var ElementResolverInterface
     */
    private $elementResolver;

    /**
     * @var LinkBuilder
     */
    private $linkBuilder;

    /**
     * @var ElementLinkFactory
     */
    private $elementLinkFactory;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    public function __construct(
        SourceCodeHighlighterInterface $highlighter,
        ElementResolverInterface $elementResolver,
        LinkBuilder $linkBuilder,
        ElementLinkFactory $elementLinkFactory,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->highlighter = $highlighter;
        $this->elementResolver = $elementResolver;
        $this->linkBuilder = $linkBuilder;
        $this->elementLinkFactory = $elementLinkFactory;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * Tries to parse a definition of a class/method/property/constant/function
     * and returns the appropriate link if successful.
     */
    public function resolveLink(string $definition, ElementReflectionInterface $reflectionElement): ?string
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

        $link = $this->elementLinkFactory->createForElement($element, $classes);

        return '<code>' . $link . $suffix . '</code>';
    }

    /**
     * @todo refactor to annotation collector
     */
    public function annotation(string $value, string $name, ElementReflectionInterface $reflectionElement): string
    {
        $annotationProcessors = [
            'return' => $this->processReturnAnnotations($value, $reflectionElement),
            'throws' => $this->processThrowsAnnotations($value, $reflectionElement),
            'license' => $this->processLicenseAnnotations($value),
            'link' => $this->processLinkAnnotations($value),
            'see' => $this->processSeeAnnotations($value, $reflectionElement),
            'uses' => $this->processUsesAnnotations($value, $reflectionElement),
        ];

        if (isset($annotationProcessors[$name])) {
            return $annotationProcessors[$name];
        }

        return $this->doc($value, $reflectionElement);
    }

    /**
     * Returns links for types.
     */
    public function typeLinks(string $annotation, ElementReflectionInterface $reflectionElement): string
    {
        $links = [];

        // typehints can not contain spaces
        // valid typehint is:
        // [TYPE[|TYPE[|...]][SPACE[METHOD|PARAM][DESCRIPTION]]
        $parts = explode(' ', $annotation);

        foreach (explode('|', $parts[0]) as $type) {
            $type = $this->getTypeName($type, false);
            $links[] = $this->resolveLink($type, $reflectionElement) ?: LatteFilters::escapeHtml(ltrim($type, '\\'));
        }

        return implode('|', $links);
    }

    public function annotationDescription(string $annotation, ElementReflectionInterface $reflectionElement): string
    {
        $description = trim(strpbrk($annotation, "\n\r\t $")) ?: $annotation;
        return $this->doc($description, $reflectionElement);
    }

    public function description(ElementReflectionInterface $element): string
    {
        $long = $element->getDescription();

        // Merge lines
        $long = preg_replace_callback('~(?:<(code|pre)>.+?</\1>)|([^<]*)~s', function ($matches) {
            return ! empty($matches[2])
                ? preg_replace('~\n(?:(\s+\n){2,})+~', ' ', $matches[2])
                : $matches[0];
        }, $long);

        return $this->doc($long, $element);
    }

    public function doc(string $text, ElementReflectionInterface $reflectionElement): string
    {
        $text = $this->resolveInternalAnnotation($text);

        $processDocTextEvent = new ProcessDocTextEvent($text, $reflectionElement);
        $this->eventDispatcher->dispatch(ProcessDocTextEvent::class, $processDocTextEvent);

        return $this->resolveLinkAndSeeAnnotation($processDocTextEvent->getText(), $reflectionElement);
    }

    private function resolveInternalAnnotation(string $text): string
    {
        $pattern = '~\\{@(\\w+)(?:(?:\\s+((?>(?R)|[^{}]+)*)\\})|\\})~';
        return preg_replace_callback($pattern, function ($matches) {
            if ($matches[1] !== 'internal') {
                return $matches[0];
            }

            return '';
        }, $text);
    }

    private function resolveLinkAndSeeAnnotation(string $text, ElementReflectionInterface $reflectionElement): string
    {
        return preg_replace_callback('~{@(?:link|see)\\s+([^}]+)}~', function ($matches) use ($reflectionElement) {
            [$url, $description] = Strings::split($matches[1]);

            $link = $this->resolveLink($matches[1], $reflectionElement);
            if ($link) {
                return $link;
            }

            if (Validators::isUri($url)) {
                return $this->linkBuilder->build($url, $description ?: $url);
            }

            return $matches[1];
        }, $text);
    }

    public function highlightPhp(string $source, ElementReflectionInterface $reflectionElement): string
    {
        return $this->resolveLink($this->getTypeName($source), $reflectionElement)
            ?: $this->highlighter->highlight($source);
    }

    public function highlightValue(string $definition, ElementReflectionInterface $reflectionElement): string
    {
        return $this->highlightPhp(preg_replace('~^(?:[ ]{4}|\t)~m', '', $definition), $reflectionElement);
    }

    private function processReturnAnnotations(string $value, ElementReflectionInterface $reflectionElement): string
    {
        $description = $this->getDescriptionFromValue($value, $reflectionElement);
        $typeLinks = $this->typeLinks($value, $reflectionElement);
        return $typeLinks . $description;
    }

    private function processThrowsAnnotations(string $value, ElementReflectionInterface $elementReflection): string
    {
        $description = $this->getDescriptionFromValue($value, $elementReflection);
        $typeLinks = $this->typeLinks($value, $elementReflection);
        return $typeLinks . $description;
    }

    /**
     * @param mixed $value
     * @param ElementReflectionInterface $elementReflection
     */
    private function getDescriptionFromValue($value, ElementReflectionInterface $elementReflection): string
    {
        $description = (string) trim((string) strpbrk($value, "\n\r\t $")) ?: null;
        if ($description) {
            $description = '<br>' . $this->doc($description, $elementReflection);
        }

        return (string) $description;
    }

    private function processLicenseAnnotations(string $value): string
    {
        [$url, $description] = Strings::split($value);
        return $this->linkBuilder->build($url, $description ?: $url);
    }

    private function processLinkAnnotations(string $value): string
    {
        [$url, $description] = Strings::split($value);
        if (Validators::isUrl($url)) {
            return $this->linkBuilder->build($url, $description ?: $url);
        }

        return '';
    }

    private function processSeeAnnotations(string $value, ElementReflectionInterface $reflectionElement): string
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

    private function processUsesAnnotations(string $value, ElementReflectionInterface $reflectionElement): ?string
    {
        [$link, $description] = Strings::split($value);
        $separator = $reflectionElement instanceof ClassReflectionInterface || ! $description ? ' ' : '<br>';
        if ($this->elementResolver->resolveElement($link, $reflectionElement) !== null) {
            $value = $this->typeLinks($link, $reflectionElement) . $separator . $description;
            return trim($value);
        }

        return null;
    }
}
