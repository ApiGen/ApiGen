<?php declare(strict_types=1);

namespace ApiGen\Templating\Filters;

use ApiGen\Contracts\Generator\SourceCodeHighlighter\SourceCodeHighlighterInterface;
use ApiGen\Reflection\Contract\Reflection\AbstractReflectionInterface;
use Symplify\ModularLatteFilters\Contract\DI\LatteFiltersProviderInterface;

final class PhpHighlighterFilters implements LatteFiltersProviderInterface
{
    /**
     * @var SourceCodeHighlighterInterface
     */
    private $highlighter;

    public function __construct(SourceCodeHighlighterInterface $highlighter)
    {
        $this->highlighter = $highlighter;
    }

    /**
     * @return callable[]
     */
    public function getFilters(): array
    {
        // @todo: detect function in template first
        return [
            'typeLinks' => function (string $definition, AbstractReflectionInterface $reflection): ?string {
                return $this->resolveLink($definition, $reflection);
            },
            'higlightValue' => function (string $definition, AbstractReflectionInterface $reflectionElement) : string {
                return $this->highlighter->highlight(
                    preg_replace('~^(?:[ ]{4}|\t)~m', '', $definition)
                );
            }
        ];
    }
}
