<?php declare(strict_types=1);

namespace ApiGen\Generator;

use ApiGen\Contracts\Configuration\ConfigurationInterface;
use ApiGen\Contracts\Generator\GeneratorInterface;
use ApiGen\Contracts\Generator\SourceCodeHighlighter\SourceCodeHighlighterInterface;
use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\TraitReflectionInterface;
use ApiGen\Contracts\Templating\TemplateRendererInterface;
use ApiGen\Reflection\Contract\ReflectionStorageInterface;

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
     * @var TemplateRendererInterface
     */
    private $templateRenderer;

    public function __construct(
        ReflectionStorageInterface $reflectionStorage,
        ConfigurationInterface $configuration,
        SourceCodeHighlighterInterface $sourceCodeHighlighter,
        TemplateRendererInterface $templateRenderer
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
                'tree' => array_merge(array_reverse($traitReflection->getParentClasses()), [$traitReflection]),
            ]
        );
    }

    private function generateSourceCodeForTrait(ClassReflectionInterface $traitReflection): void
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
