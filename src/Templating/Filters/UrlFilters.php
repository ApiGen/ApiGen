<?php declare(strict_types=1);

namespace ApiGen\Templating\Filters;

use ApiGen\Contracts\Generator\SourceCodeHighlighter\SourceCodeHighlighterInterface;
use ApiGen\Reflection\Contract\Reflection\AbstractReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Class_\ClassReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Function_\FunctionReflectionInterface;
use ApiGen\Event\ProcessDocTextEvent;
use ApiGen\StringRouting\Route\ReflectionRoute;
use ApiGen\StringRouting\StringRouter;
use ApiGen\Templating\Filters\Helpers\LinkBuilder;
use ApiGen\Templating\Filters\Helpers\Strings;
use Latte\Runtime\Filters as LatteFilters;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symplify\ModularLatteFilters\Contract\DI\LatteFiltersProviderInterface;

final class UrlFilters implements LatteFiltersProviderInterface
{
    /**
     * @var SourceCodeHighlighterInterface
     */
    private $highlighter;

    /**
     * @var LinkBuilder
     */
    private $linkBuilder;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var StringRouter
     */
    private $stringRouter;

    public function __construct(
        SourceCodeHighlighterInterface $highlighter,
        LinkBuilder $linkBuilder,
        StringRouter $stringRouter,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->highlighter = $highlighter;
        $this->linkBuilder = $linkBuilder;
        $this->eventDispatcher = $eventDispatcher;
        $this->stringRouter = $stringRouter;
    }

    /**
     * @return callable[]
     */
    public function getFilters(): array
    {
        return [
            'annotation' => function (string $value, string $name, AbstractReflectionInterface $reflection): string {
                return $this->annotation( $value, $name, $reflection);
            },
            'typeLinks' => function (string $definition, AbstractReflectionInterface $reflection): ?string {
                return $this->resolveLink($definition, $reflection);
            }
        ];
    }

    private function annotation(string $value, string $name, AbstractReflectionInterface $reflection): string
    {
        // todo: split to collector or dispatcher
        $annotationProcessors = [
            'return' => $this->processReturnAnnotations($value, $reflection),
            'throws' => $this->processThrowsAnnotations($value, $reflection),
            'uses' => $this->processUsesAnnotations($value, $reflection),
            // @todo: @covers
        ];

        if (isset($annotationProcessors[$name])) {
            return $annotationProcessors[$name];
        }

        return $this->doc($value, $reflection);
    }

    /**
     * Returns links for types.
     */
    private function typeLinks(string $annotation, AbstractReflectionInterface $reflection): string
    {
        $links = [];

        // typehints can not contain spaces
        // valid typehint is:
        // [TYPE[|TYPE[|...]][SPACE[METHOD|PARAM][DESCRIPTION]]
        $parts = explode(' ', $annotation);

        foreach (explode('|', $parts[0]) as $type) {
            $type = $this->getTypeName($type, false);
            $links[] = $this->resolveLink($type, $reflection) ?: LatteFilters::escapeHtml(ltrim($type, '\\'));
        }

        return implode('|', $links);
    }

    /**
     * Tries to parse a definition of a class/method/property/constant/function
     * and returns the appropriate link if successful.
     */
    private function resolveLink(string $definition, AbstractReflectionInterface $reflection): ?string
    {
        if (empty($definition)) {
            return null;
        }

        $suffix = '';
        if (substr($definition, -2) === '[]') {
            $definition = substr($definition, 0, -2);
            $suffix = '[]';
        }

        $element = $this->elementResolver->resolveElement($definition, $reflection, $expectedName);
        if ($element === null || $element instanceof FunctionReflectionInterface) {
            return $expectedName;
        }

        $classes = [];
        if ($element->isDeprecated()) {
            $classes[] = 'deprecated';
        }

        /** @var AbstractReflectionInterface $element */
        $url = $this->stringRouter->buildRoute(ReflectionRoute::NAME, $element);
        $link = $this->linkBuilder->build($url, $element->getName(), true, $classes);

        return '<code>' . $link . $suffix . '</code>';
    }

    public function annotationDescription(string $annotation, AbstractReflectionInterface $reflection): string
    {
        $description = trim(strpbrk($annotation, "\n\r\t $")) ?: $annotation;
        return $this->doc($description, $reflection);
    }

    private function doc(string $text, AbstractReflectionInterface $reflection): string
    {
        $processDocTextEvent = new ProcessDocTextEvent($text, $reflection);
        $this->eventDispatcher->dispatch(ProcessDocTextEvent::class, $processDocTextEvent);

        return $processDocTextEvent->getText();
    }

    public function highlightPhp(string $source, AbstractReflectionInterface $reflectionElement): string
    {
        return $this->resolveLink($this->getTypeName($source), $reflectionElement)
            ?: $this->highlighter->highlight($source);
    }

    public function highlightValue(string $definition, AbstractReflectionInterface $reflectionElement): string
    {
        return $this->highlightPhp(preg_replace('~^(?:[ ]{4}|\t)~m', '', $definition), $reflectionElement);
    }

    private function processReturnAnnotations(string $value, AbstractReflectionInterface $reflectionElement): string
    {
        $description = $this->getDescriptionFromValue($value, $reflectionElement);
        $typeLinks = $this->typeLinks($value, $reflectionElement);
        return $typeLinks . $description;
    }

    private function processThrowsAnnotations(string $value, AbstractReflectionInterface $Reflection): string
    {
        $description = $this->getDescriptionFromValue($value, $Reflection);
        $typeLinks = $this->typeLinks($value, $Reflection);
        return $typeLinks . $description;
    }

    /**
     * @param mixed $value
     */
    private function getDescriptionFromValue($value, AbstractReflectionInterface $reflection): string
    {
        $description = (string) trim((string) strpbrk($value, "\n\r\t $")) ?: null;
        if ($description) {
            $description = '<br>' . $this->doc($description, $reflection);
        }

        return (string) $description;
    }


    private function processUsesAnnotations(string $value, AbstractReflectionInterface $reflection): ?string
    {
        [$link, $description] = Strings::split($value);
        $separator = $reflection instanceof ClassReflectionInterface || ! $description ? ' ' : '<br>';
        if ($this->elementResolver->resolveElement($link, $reflection) !== null) {
            $value = $this->typeLinks($link, $reflection) . $separator . $description;
            return trim($value);
        }

        return null;
    }

}
