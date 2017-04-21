<?php declare(strict_types=1);

namespace ApiGen\Generator\TemplateGenerators;

use ApiGen\Contracts\Configuration\ConfigurationInterface;
use ApiGen\Contracts\Generator\NamedDestinationGeneratorInterface;
use ApiGen\Contracts\Generator\SourceCodeHighlighter\SourceCodeHighlighterInterface;
use ApiGen\Contracts\Parser\Elements\ElementStorageInterface;
use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Templating\TemplateFactory\TemplateFactoryInterface;
use ApiGen\Generator\Resolvers\RelativePathResolver;

final class TraitGenerator implements NamedDestinationGeneratorInterface
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
        foreach ($this->elementStorage->getTraits() as $traitReflection) {
            $this->generateForTrait($traitReflection);
            $this->generateSourceCodeForTrait($traitReflection);
        }
    }

    public function getDestinationPath(string $traitName): string
    {
        return $this->configuration->getDestinationWithPrefixName('trait-',$traitName);
    }

    private function generateForTrait(ClassReflectionInterface $traitReflection): void
    {
        $template = $this->templateFactory->create();
        $template->setFile($this->configuration->getTemplateByName('trait'));

        $template->save($this->getDestinationPath($traitReflection->getName()), [
            'trait' => $traitReflection,
            'tree' => array_merge(array_reverse($traitReflection->getParentClasses()), [$traitReflection]),
        ]);
    }

    private function generateSourceCodeForTrait(ClassReflectionInterface $traitReflection)
    {
        $template = $this->templateFactory->create();
        $template->setFile($this->configuration->getTemplateByName('source'));

        $content = file_get_contents($traitReflection->getFileName());
        $highlightedContent = $this->sourceCodeHighlighter->highlightAndAddLineNumbers($content);

        $destination = $this->configuration->getDestinationWithPrefixName('source-trait-', $traitReflection->getName());

        $template->save($destination, [
            'fileName' => $this->relativePathResolver->getRelativePath($traitReflection->getFileName()),
            'source' => $highlightedContent,
        ]);
    }
}
