<?php declare(strict_types=1);

namespace ApiGen\Generator;

use ApiGen\Configuration\Configuration;
use ApiGen\Contract\Generator\GeneratorInterface;
use ApiGen\Reflection\Contract\Reflection\Interface_\InterfaceReflectionInterface;
use ApiGen\Reflection\ReflectionStorage;
use ApiGen\SourceCodeHighlighter\SourceCodeHighlighter;
use ApiGen\Templating\TemplateRenderer;

final class InterfaceGenerator implements GeneratorInterface
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
        foreach ($this->reflectionStorage->getInterfaceReflections() as $interfaceReflection) {
            $this->generateForInterface($interfaceReflection);
            if ($interfaceReflection->getFileName()) {
                $this->generateSourceCodeForInterface($interfaceReflection);
            }
        }
    }

    private function generateForInterface(InterfaceReflectionInterface $interfaceReflection): void
    {
        $this->templateRenderer->renderToFile(
            $this->configuration->getTemplateByName('interface'),
            $this->configuration->getDestinationWithPrefixName('interface-', $interfaceReflection->getName()),
            [
                'activePage' => 'interface',
                'interface' => $interfaceReflection,
            ]
        );
    }

    private function generateSourceCodeForInterface(InterfaceReflectionInterface $interfaceReflection): void
    {
        $content = file_get_contents($interfaceReflection->getFileName());
        $highlightedContent = $this->sourceCodeHighlighter->highlightAndAddLineNumbers($content);

        $destination = $this->configuration->getDestinationWithPrefixName(
            'source-interface-',
            $interfaceReflection->getName()
        );

        $this->templateRenderer->renderToFile(
            $this->configuration->getTemplateByName('source'),
            $destination,
            [
                'activePage' => 'interface',
                'fileName' => $interfaceReflection->getFileName(),
                'source' => $highlightedContent,
            ]
        );
    }
}
