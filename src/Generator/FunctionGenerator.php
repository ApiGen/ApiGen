<?php declare(strict_types=1);

namespace ApiGen\Generator;

use ApiGen\Contracts\Configuration\ConfigurationInterface;
use ApiGen\Contracts\Generator\GeneratorInterface;
use ApiGen\Contracts\Generator\SourceCodeHighlighter\SourceCodeHighlighterInterface;
use ApiGen\Reflection\Contract\Reflection\FunctionReflectionInterface;
use ApiGen\Contracts\Templating\TemplateRendererInterface;
use ApiGen\Reflection\Contract\ReflectionStorageInterface;

final class FunctionGenerator implements GeneratorInterface
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
        foreach ($this->reflectionStorage->getFunctionReflections() as $reflectionFunction) {
            $this->generateForFunction($reflectionFunction);
            $this->generateSourceCodeForFunction($reflectionFunction);
        }
    }

    private function generateForFunction(FunctionReflectionInterface $reflectionFunction): void
    {
        $this->templateRenderer->renderToFile(
            $this->configuration->getTemplateByName('function'),
            $this->configuration->getDestinationWithPrefixName('function-', $reflectionFunction->getName()),
            [
                'function' => $reflectionFunction
            ]
        );
    }

    private function generateSourceCodeForFunction(FunctionReflectionInterface $functionReflection): void
    {
        $content = file_get_contents($functionReflection->getFileName());
        $highlightedContent = $this->sourceCodeHighlighter->highlightAndAddLineNumbers($content);

        $destination = $this->configuration->getDestinationWithPrefixName(
            'source-function-', $functionReflection->getName()
        );

        $this->templateRenderer->renderToFile(
            $this->configuration->getTemplateByName('source'),
            $destination,
            [
                'fileName' => $functionReflection->getFileName(),
                'source' => $highlightedContent,
            ]
        );
    }
}
