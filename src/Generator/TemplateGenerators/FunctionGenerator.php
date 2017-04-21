<?php declare(strict_types=1);

namespace ApiGen\Generator\TemplateGenerators;

use ApiGen\Contracts\Configuration\ConfigurationInterface;
use ApiGen\Contracts\Generator\NamedDestinationGeneratorInterface;
use ApiGen\Contracts\Generator\SourceCodeHighlighter\SourceCodeHighlighterInterface;
use ApiGen\Contracts\Parser\Elements\ElementStorageInterface;
use ApiGen\Contracts\Parser\Reflection\FunctionReflectionInterface;
use ApiGen\Contracts\Templating\TemplateFactory\TemplateFactoryInterface;
use ApiGen\Generator\Resolvers\RelativePathResolver;
use PHP_CodeSniffer\Reports\Source;

final class FunctionGenerator implements NamedDestinationGeneratorInterface
{
    /**
     * @var TemplateFactoryInterface
     */
    private $templateFactory;

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

    public function __construct(
        TemplateFactoryInterface $templateFactory,
        ElementStorageInterface $elementStorage,
        ConfigurationInterface $configuration,
        SourceCodeHighlighterInterface $sourceCodeHighlighter,
        RelativePathResolver $relativePathResolver
    ) {
        $this->templateFactory = $templateFactory;
        $this->elementStorage = $elementStorage;
        $this->configuration = $configuration;
        $this->sourceCodeHighlighter = $sourceCodeHighlighter;
        $this->relativePathResolver = $relativePathResolver;
    }

    public function generate(): void
    {
        foreach ($this->elementStorage->getFunctions() as $reflectionFunction) {
            $this->generateForFunction($reflectionFunction);
            $this->generateSourceCodeForFunction($reflectionFunction);
        }
    }

    public function getDestinationPath(string $functionName): string
    {
        return $this->configuration->getDestinationWithPrefixName('function-', $functionName);
    }

    private function generateForFunction(FunctionReflectionInterface $reflectionFunction): void
    {
        $template = $this->templateFactory->create();
        $template->setFile($this->configuration->getTemplateByName('function'));

        $template->save($this->getDestinationPath($reflectionFunction->getName()), [
            'function' => $reflectionFunction
        ]);
    }

    private function generateSourceCodeForFunction(FunctionReflectionInterface $functionReflection): void
    {
        $template = $this->templateFactory->create();
        $template->setFile($this->configuration->getTemplateByName('source'));

        $content = file_get_contents($functionReflection->getFileName());
        $highlightedContent = $this->sourceCodeHighlighter->highlightAndAddLineNumbers($content);

        $destination = $this->configuration->getDestinationWithPrefixName(
            'source-function-', $functionReflection->getName()
        );

        $template->save($destination, [
            'fileName' => $this->relativePathResolver->getRelativePath($functionReflection->getFileName()),
            'source' => $highlightedContent,
        ]);
    }
}
