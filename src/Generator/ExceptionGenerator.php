<?php declare(strict_types=1);

namespace ApiGen\Generator;

use ApiGen\Configuration\Configuration;
use ApiGen\Contract\Generator\GeneratorInterface;
use ApiGen\Reflection\Contract\Reflection\Class_\ClassReflectionInterface;
use ApiGen\Reflection\ReflectionStorage;
use ApiGen\SourceCodeHighlighter\SourceCodeHighlighter;
use ApiGen\Templating\TemplateRenderer;

final class ExceptionGenerator implements GeneratorInterface
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
        foreach ($this->reflectionStorage->getExceptionReflections() as $exceptionReflection) {
            $this->generateForException($exceptionReflection);
            if ($exceptionReflection->getFileName()) {
                $this->generateSourceCodeForException($exceptionReflection);
            }
        }
    }

    private function generateForException(ClassReflectionInterface $classReflection): void
    {
        $this->templateRenderer->renderToFile(
            $this->configuration->getTemplateByName('class'),
            $this->configuration->getDestinationWithPrefixName('exception-', $classReflection->getName()),
            [
                'activePage' => 'exception',
                'class' => $classReflection,
            ]
        );
    }

    private function generateSourceCodeForException(ClassReflectionInterface $classReflection): void
    {
        $content = file_get_contents($classReflection->getFileName());
        $highlightedContent = $this->sourceCodeHighlighter->highlightAndAddLineNumbers($content);

        $this->templateRenderer->renderToFile(
            $this->configuration->getTemplateByName('source'),
            $this->configuration->getDestinationWithPrefixName('source-exception-', $classReflection->getName()),
            [
                'activePage' => 'class',
                'activeClass' => $classReflection,
                'fileName' => $classReflection->getFileName(),
                'source' => $highlightedContent,
            ]
        );
    }
}
