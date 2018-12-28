<?php declare(strict_types=1);

namespace ApiGen\Generator;

use ApiGen\Configuration\Configuration;
use ApiGen\Contract\Generator\GeneratorInterface;
use ApiGen\Reflection\Contract\Reflection\Function_\FunctionReflectionInterface;
use ApiGen\Reflection\ReflectionStorage;
use ApiGen\SourceCodeHighlighter\SourceCodeHighlighter;
use ApiGen\Templating\TemplateRenderer;
use ApiGen\Utils\RelativePathResolver;

final class FunctionGenerator implements GeneratorInterface
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

    /**
     * @var RelativePathResolver
     */
    private $relativePathResolver;

    public function __construct(
        ReflectionStorage $reflectionStorage,
        Configuration $configuration,
        SourceCodeHighlighter $sourceCodeHighlighter,
        TemplateRenderer $templateRenderer,
        RelativePathResolver $relativePathResolver
    ) {
        $this->reflectionStorage = $reflectionStorage;
        $this->configuration = $configuration;
        $this->sourceCodeHighlighter = $sourceCodeHighlighter;
        $this->templateRenderer = $templateRenderer;
        $this->relativePathResolver = $relativePathResolver;
    }

    public function generate(): void
    {
        foreach ($this->reflectionStorage->getFunctionReflections() as $functionReflection) {
            $this->generateForFunction($functionReflection);
            if ($functionReflection->getFileName()) {
                $this->generateSourceCodeForFunction($functionReflection);
            }
        }
    }

    private function generateForFunction(FunctionReflectionInterface $functionReflection): void
    {
        $this->templateRenderer->renderToFile(
            $this->configuration->getTemplateByName('function'),
            $this->configuration->getDestinationWithPrefixName('function-', $functionReflection->getName()),
            [
                'activePage' => 'function',
                'function' => $functionReflection,
            ]
        );
    }

    private function generateSourceCodeForFunction(FunctionReflectionInterface $functionReflection): void
    {
        $content = file_get_contents($functionReflection->getFileName());
        $highlightedContent = $this->sourceCodeHighlighter->highlightAndAddLineNumbers($content);

        $relativePath = $this->relativePathResolver->getRelativePath($functionReflection->getFileName());

        $destination = $this->configuration->getDestinationWithPrefixName('source-function-', $relativePath);

        if (file_exists($destination)) {
            return;
        }

        $this->templateRenderer->renderToFile(
            $this->configuration->getTemplateByName('source'),
            $destination,
            [
                'activePage' => 'function',
                'fileName' => $functionReflection->getFileName(),
                'source' => $highlightedContent,
            ]
        );
    }
}
