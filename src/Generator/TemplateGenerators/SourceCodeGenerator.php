<?php declare(strict_types=1);

namespace ApiGen\Generator\TemplateGenerators;

use ApiGen\Contracts\Configuration\ConfigurationInterface;
use ApiGen\Contracts\Generator\SourceCodeHighlighter\SourceCodeHighlighterInterface;
use ApiGen\Contracts\Generator\TemplateGenerators\TemplateGeneratorInterface;
use ApiGen\Contracts\Parser\Elements\ElementStorageInterface;
use ApiGen\Contracts\Parser\Reflection\ElementReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\TokenReflection\ReflectionInterface;
use ApiGen\Generator\Resolvers\RelativePathResolver;
use ApiGen\Parser\Reflection\AbstractReflection;
use ApiGen\Templating\Filters\SourceFilters;
use ApiGen\Templating\TemplateFactory;

final class SourceCodeGenerator implements TemplateGeneratorInterface
{
    /**
     * @var ElementStorageInterface
     */
    private $elementStorage;

    /**
     * @var TemplateFactory
     */
    private $templateFactory;

    /**
     * @var RelativePathResolver
     */
    private $relativePathResolver;

    /**
     * @var SourceCodeHighlighterInterface
     */
    private $sourceCodeHighlighter;

    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     * @var SourceFilters
     */
    private $sourceFilters;

    public function __construct(
        ElementStorageInterface $elementStorage,
        TemplateFactory $templateFactory,
        RelativePathResolver $relativePathResolver,
        SourceCodeHighlighterInterface $sourceCodeHighlighter,
        ConfigurationInterface $configuration,
        SourceFilters $sourceFilters
    ) {
        $this->elementStorage = $elementStorage;
        $this->templateFactory = $templateFactory;
        $this->relativePathResolver = $relativePathResolver;
        $this->sourceCodeHighlighter = $sourceCodeHighlighter;
        $this->configuration = $configuration;
        $this->sourceFilters = $sourceFilters;
    }

    public function generate(): void
    {
        foreach ($this->elementStorage->getElements() as $type => $elementList) {
            foreach ($elementList as $element) {
                /** @var ElementReflectionInterface $element */
                $this->generateForElement($element);
            }
        }
    }

    /**
     * @param ElementReflectionInterface|AbstractReflection $element
     */
    private function generateForElement(ElementReflectionInterface $element): void
    {
        $template = $this->templateFactory->create();
        $template->setFile($this->getTemplateFile());
        $template->save($this->getDestination($element), [
            'fileName' => $this->relativePathResolver->getRelativePath($element->getFileName()),
            'source' => $this->getHighlightedCodeFromElement($element)
        ]);
    }

    private function getHighlightedCodeFromElement(AbstractReflection $element): string
    {
        $content = file_get_contents($element->getFileName());

        return $this->sourceCodeHighlighter->highlightAndAddLineNumbers($content);
    }

    private function getTemplateFile(): string
    {
        return $this->configuration->getTemplatesDirectory()
            . DIRECTORY_SEPARATOR
            . 'source.latte';
    }

    private function getDestination(ReflectionInterface $reflection): string
    {
        return $this->configuration->getDestination()
            . DIRECTORY_SEPARATOR
            . $this->sourceFilters->sourceUrl($reflection, false)
        ;
    }
}
