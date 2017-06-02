<?php declare(strict_types=1);

namespace ApiGen\Generator;

use ApiGen\Contract\Configuration\ConfigurationInterface;
use ApiGen\Contract\Generator\GeneratorInterface;
use ApiGen\Contract\Generator\SourceCodeHighlighter\SourceCodeHighlighterInterface;
use ApiGen\Reflection\Contract\Reflection\Trait_\TraitReflectionInterface;
use ApiGen\Reflection\Contract\ReflectionStorageInterface;
use ApiGen\Templating\TemplateRenderer;

final class TraitGenerator implements GeneratorInterface
{
    /**
     * @var ReflectionStorageInterface
     */
    private $reflectionStorage;

    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     * @var SourceCodeHighlighterInterface
     */
    private $sourceCodeHighlighter;

    /**
     * @var TemplateRenderer
     */
    private $templateRenderer;

    public function __construct(
        ReflectionStorageInterface $reflectionStorage,
        ConfigurationInterface $configuration,
        SourceCodeHighlighterInterface $sourceCodeHighlighter,
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
            $this->generateSourceCodeForTrait($traitReflection);
        }
    }

    private function generateForTrait(TraitReflectionInterface $traitReflection): void
    {
        $this->templateRenderer->renderToFile(
            $this->configuration->getTemplateByName('trait'),
            $this->configuration->getDestinationWithPrefixName('trait-',$traitReflection->getName()),
            [
                'trait' => $traitReflection,
            ]
        );
    }

    private function generateSourceCodeForTrait(TraitReflectionInterface $traitReflection): void
    {
        $content = file_get_contents($traitReflection->getFileName());
        $highlightedContent = $this->sourceCodeHighlighter->highlightAndAddLineNumbers($content);

        $destination = $this->configuration->getDestinationWithPrefixName('source-trait-', $traitReflection->getName());

        $this->templateRenderer->renderToFile(
            $this->configuration->getTemplateByName('source'),
            $destination,
            [
                'fileName' => $traitReflection->getFileName(),
                'source' => $highlightedContent,
            ]
        );
    }
}
