<?php declare(strict_types=1);

namespace ApiGen\Generator\TemplateGenerators;

use ApiGen\Contracts\Configuration\ConfigurationInterface;
use ApiGen\Contracts\Generator\GeneratorInterface;
use ApiGen\Contracts\Generator\SourceCodeHighlighter\SourceCodeHighlighterInterface;
use ApiGen\Contracts\Parser\Elements\ElementStorageInterface;
use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Templating\TemplateRendererInterface;
use ApiGen\Generator\Resolvers\RelativePathResolver;

final class ClassGenerator implements GeneratorInterface
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
     * @var RelativePathResolver
     */
    private $relativePathResolver;

    /**
     * @var TemplateRendererInterface
     */
    private $templateRenderer;

    public function __construct(
        ElementStorageInterface $elementStorage,
        ConfigurationInterface $configuration,
        SourceCodeHighlighterInterface $sourceCodeHighlighter,
        RelativePathResolver $relativePathResolver,
        TemplateRendererInterface $templateRenderer
    ) {
        $this->elementStorage = $elementStorage;
        $this->configuration = $configuration;
        $this->sourceCodeHighlighter = $sourceCodeHighlighter;
        $this->relativePathResolver = $relativePathResolver;
        $this->templateRenderer = $templateRenderer;
    }

    public function generate(): void
    {
        foreach ($this->elementStorage->getClasses() as $classReflection) {
            $this->generateForClass($classReflection);
            $this->generateSourceCodeForClass($classReflection);
        }
    }

    private function generateForClass(ClassReflectionInterface $classReflection): void
    {
        $this->templateRenderer->renderToFile(
            $this->configuration->getTemplateByName('class'),
            $this->configuration->getDestinationWithPrefixName('class-', $classReflection->getName()),
            [
                'class' => $classReflection,
                'tree' => array_merge(array_reverse($classReflection->getParentClasses()), [$classReflection]),
            ]
        );
    }

    private function generateSourceCodeForClass(ClassReflectionInterface $classReflection): void
    {
        $content = file_get_contents($classReflection->getFileName());
        $highlightedContent = $this->sourceCodeHighlighter->highlightAndAddLineNumbers($content);

        $this->templateRenderer->renderToFile(
            $this->configuration->getTemplateByName('source'),
            $this->configuration->getDestinationWithPrefixName('source-class-', $classReflection->getName()),
            [
                'fileName' => $this->relativePathResolver->getRelativePath($classReflection->getFileName()),
                'source' => $highlightedContent
            ]
        );
    }
}
