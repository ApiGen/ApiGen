<?php declare(strict_types=1);

namespace ApiGen\Templating\Filters;

use ApiGen\Annotation\AnnotationDecorator;
use ApiGen\Contracts\Generator\SourceCodeHighlighter\SourceCodeHighlighterInterface;
use ApiGen\Reflection\Contract\Reflection\AbstractReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Function_\FunctionReflectionInterface;
use ApiGen\StringRouting\Route\ReflectionRoute;
use ApiGen\StringRouting\StringRouter;
use ApiGen\Templating\Filters\Helpers\LinkBuilder;
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
     * @var StringRouter
     */
    private $stringRouter;

    public function __construct(
        SourceCodeHighlighterInterface $highlighter,
        LinkBuilder $linkBuilder,
        StringRouter $stringRouter,
        EventDispatcherInterface $eventDispatcher,
        AnnotationDecorator $annotationDecorator
    ) {
        $this->highlighter = $highlighter;
        $this->linkBuilder = $linkBuilder;
        $this->stringRouter = $stringRouter;
    }

    /**
     * @return callable[]
     */
    public function getFilters(): array
    {
        return [
            'typeLinks' => function (string $definition, AbstractReflectionInterface $reflection): ?string {
                return $this->resolveLink($definition, $reflection);
            }
        ];
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

    public function highlightPhp(string $source, AbstractReflectionInterface $reflectionElement): string
    {
        return $this->resolveLink($this->getTypeName($source), $reflectionElement)
            ?: $this->highlighter->highlight($source);
    }

    public function highlightValue(string $definition, AbstractReflectionInterface $reflectionElement): string
    {
        return $this->highlightPhp(preg_replace('~^(?:[ ]{4}|\t)~m', '', $definition), $reflectionElement);
    }
}
