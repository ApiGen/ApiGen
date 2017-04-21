<?php declare(strict_types=1);

namespace ApiGen\Generator\TemplateGenerators;

use ApiGen\Contracts\Configuration\ConfigurationInterface;
use ApiGen\Contracts\Generator\GeneratorInterface;
use ApiGen\Contracts\Generator\SourceCodeHighlighter\SourceCodeHighlighterInterface;
use ApiGen\Contracts\Parser\Elements\ElementStorageInterface;
use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Templating\TemplateRendererInterface;

final class TraitGenerator implements GeneratorInterface
{
    /**
     * @var ElementStorageInterface
     */
    private $elementStorage;

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
        ElementStorageInterface $elementStorage,
        ConfigurationInterface $configuration,
        SourceCodeHighlighterInterface $sourceCodeHighlighter,
        TemplateRendererInterface $templateRenderer
    ) {
        $this->elementStorage = $elementStorage;
        $this->configuration = $configuration;
        $this->sourceCodeHighlighter = $sourceCodeHighlighter;
        $this->templateRenderer = $templateRenderer;
    }

    public function generate(): void
    {
        foreach ($this->elementStorage->getTraits() as $traitReflection) {
            $this->generateForTrait($traitReflection);
            $this->generateSourceCodeForTrait($traitReflection);
        }
    }

    private function generateForTrait(ClassReflectionInterface $traitReflection): void
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
