<?php declare(strict_types=1);

namespace ApiGen\Generator;

use ApiGen\Contracts\Configuration\ConfigurationInterface;
use ApiGen\Contracts\Generator\GeneratorInterface;
use ApiGen\Contracts\Generator\SourceCodeHighlighter\SourceCodeHighlighterInterface;
use ApiGen\Reflection\Contract\Reflection\Function_\FunctionReflectionInterface;
use ApiGen\Reflection\Contract\ReflectionStorageInterface;
use ApiGen\Templating\TemplateRenderer;
use ApiGen\Utils\RelativePathResolver;

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
     * @var TemplateRenderer
     */
    private $templateRenderer;

    /**
     * @var RelativePathResolver
     */
    private $relativePathResolver;

    public function __construct(
        ReflectionStorageInterface $reflectionStorage,
        ConfigurationInterface $configuration,
        SourceCodeHighlighterInterface $sourceCodeHighlighter,
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

        $relativePath = $this->relativePathResolver->getRelativePath($functionReflection->getFileName());

        $destination = $this->configuration->getDestinationWithPrefixName(
            'source-function-', $relativePath
        );

        if (file_exists($destination)) {
            return;
        }

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
