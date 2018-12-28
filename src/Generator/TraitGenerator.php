<?php declare(strict_types=1);

namespace ApiGen\Generator;

use ApiGen\Configuration\Configuration;
use ApiGen\Contract\Generator\GeneratorInterface;
use ApiGen\Reflection\Contract\Reflection\Trait_\TraitReflectionInterface;
use ApiGen\Reflection\ReflectionStorage;
use ApiGen\SourceCodeHighlighter\SourceCodeHighlighter;
use ApiGen\Templating\TemplateRenderer;

final class TraitGenerator implements GeneratorInterface
{
    /**
     * @var ReflectionStorage
     */
    private $reflectionStorage;

    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var SourceCodeHighlighter
     */
    private $sourceCodeHighlighter;

    /**
     * @var TemplateRenderer
     */
    private $templateRenderer;

    public function __construct(
        ReflectionStorage $reflectionStorage,
        Configuration $configuration,
        SourceCodeHighlighter $sourceCodeHighlighter,
        TemplateRenderer $templateRenderer
    ) {
        $this->reflectionStorage = $reflectionStorage;
        $this->configuration = $configuration;
        $this->sourceCodeHighlighter = $sourceCodeHighlighter;
        $this->templateRenderer = $templateRenderer;
    }

    public function generate(): void
    {
        foreach ($this->reflectionStorage->getTraitReflections() as $traitReflection) {
            $this->generateForTrait($traitReflection);
            if ($traitReflection->getFileName()) {
                $this->generateSourceCodeForTrait($traitReflection);
            }
        }
    }

    private function generateForTrait(TraitReflectionInterface $traitReflection): void
    {
        $this->templateRenderer->renderToFile(
            $this->configuration->getTemplateByName('trait'),
            $this->configuration->getDestinationWithPrefixName('trait-', $traitReflection->getName()),
            [
                'activePage' => 'trait',
                'trait' => $traitReflection,
            ]
        );
    }

    private function generateSourceCodeForTrait(TraitReflectionInterface $traitReflection): void
    {
        $content = file_get_contents($traitReflection->getFileName());
        $highlightedContent = $this->sourceCodeHighlighter->highlightAndAddLineNumbers($content);

        $destination = $this->configuration->getDestinationWithPrefixName(
            'source-trait-',
            $traitReflection->getName()
        );

        $this->templateRenderer->renderToFile(
            $this->configuration->getTemplateByName('source'),
            $destination,
            [
                'activePage' => 'trait',
                'fileName' => $traitReflection->getFileName(),
                'source' => $highlightedContent,
            ]
        );
    }
}
