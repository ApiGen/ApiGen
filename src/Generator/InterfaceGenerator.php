<?php declare(strict_types=1);

namespace ApiGen\Generator;

use ApiGen\Contract\Configuration\ConfigurationInterface;
use ApiGen\Contract\Generator\GeneratorInterface;
use ApiGen\SourceCodeHighlighter\SourceCodeHighlighter;
use ApiGen\Reflection\Contract\Reflection\Interface_\InterfaceReflectionInterface;
use ApiGen\Reflection\Contract\ReflectionStorageInterface;
use ApiGen\Templating\TemplateRenderer;

final class InterfaceGenerator implements GeneratorInterface
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
     * @var SourceCodeHighlighter
     */
    private $sourceCodeHighlighter;

    /**
     * @var TemplateRenderer
     */
    private $templateRenderer;

    public function __construct(
        ReflectionStorageInterface $reflectionStorage,
        ConfigurationInterface $configuration,
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
        foreach ($this->reflectionStorage->getInterfaceReflections() as $interfaceReflection) {
            $this->generateForInterface($interfaceReflection);
            $this->generateSourceCodeForInterface($interfaceReflection);
        }
    }

    private function generateForInterface(InterfaceReflectionInterface $interfaceReflection): void
    {
        $this->templateRenderer->renderToFile(
            $this->configuration->getTemplateByName('interface'),
            $this->configuration->getDestinationWithPrefixName('interface-', $interfaceReflection->getName()),
            [
                'interface' => $interfaceReflection,
            ]
        );
    }

    private function generateSourceCodeForInterface(InterfaceReflectionInterface $interfaceReflection): void
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
