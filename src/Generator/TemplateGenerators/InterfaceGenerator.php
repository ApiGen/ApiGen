<?php declare(strict_types=1);

namespace ApiGen\Generator\TemplateGenerators;

use ApiGen\Contracts\Configuration\ConfigurationInterface;
use ApiGen\Contracts\Generator\GeneratorInterface;
use ApiGen\Contracts\Generator\SourceCodeHighlighter\SourceCodeHighlighterInterface;
use ApiGen\Contracts\Parser\Elements\ElementStorageInterface;
use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Templating\TemplateRendererInterface;

final class InterfaceGenerator implements GeneratorInterface
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
        foreach ($this->elementStorage->getInterfaces() as $interfaceReflection) {
            $this->generateForInterface($interfaceReflection);
            $this->generateSourceCodeForInterface($interfaceReflection);
        }
    }

    private function generateForInterface(ClassReflectionInterface $interfaceReflection): void
    {
        $this->templateRenderer->renderToFile(
            $this->configuration->getTemplateByName('interface'),
            $this->configuration->getDestinationWithPrefixName('interface-', $interfaceReflection->getName()),
            [
                'interface' => $interfaceReflection,
                'tree' => array_merge(array_reverse($interfaceReflection->getParentClasses()), [$interfaceReflection]),
            ]
        );
    }

    private function generateSourceCodeForInterface(ClassReflectionInterface $interfaceReflection): void
    {
        $content = file_get_contents($interfaceReflection->getFileName());
        $highlightedContent = $this->sourceCodeHighlighter->highlightAndAddLineNumbers($content);

        $destination = $this->configuration->getDestinationWithPrefixName(
            'source-interface-', $interfaceReflection->getName()
        );

        $this->templateRenderer->renderToFile(
            $this->configuration->getTemplateByName('source'),
            $destination,
            [
                'fileName' => $interfaceReflection->getFileName(),
                'source' => $highlightedContent,
            ]
        );
    }
}
