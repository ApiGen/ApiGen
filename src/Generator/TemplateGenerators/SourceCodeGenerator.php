<?php declare(strict_types=1);

namespace ApiGen\Generator\TemplateGenerators;

use ApiGen\Contracts\Configuration\ConfigurationInterface;
use ApiGen\Contracts\Generator\GeneratorInterface;
use ApiGen\Contracts\Generator\SourceCodeHighlighter\SourceCodeHighlighterInterface;
use ApiGen\Contracts\Parser\Elements\ElementStorageInterface;
use ApiGen\Contracts\Parser\Reflection\ElementReflectionInterface;
use ApiGen\Generator\Resolvers\RelativePathResolver;
use ApiGen\Parser\Reflection\AbstractReflection;
use ApiGen\Templating\TemplateFactory;

final class SourceCodeGenerator implements GeneratorInterface
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

    public function __construct(
        ElementStorageInterface $elementStorage,
        TemplateFactory $templateFactory,
        RelativePathResolver $relativePathResolver,
        SourceCodeHighlighterInterface $sourceCodeHighlighter,
        ConfigurationInterface $configuration
    ) {
        $this->elementStorage = $elementStorage;
        $this->templateFactory = $templateFactory;
        $this->relativePathResolver = $relativePathResolver;
        $this->sourceCodeHighlighter = $sourceCodeHighlighter;
        $this->configuration = $configuration;
    }

    public function generate(): void
    {
        foreach ($this->elementStorage->getElements() as $elementList) {
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
        $template->save($this->getDestinationPath($element), [
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

    public function getDestinationPath(ElementReflectionInterface $name): string
    {
        dump($name);
        die;
//        'source-class-%s.html'
//        'source-function-%s.html'
        return $this->configuration->getDestination()
            . DIRECTORY_SEPARATOR
            . sprintf(

            )
        ;
    }
}
