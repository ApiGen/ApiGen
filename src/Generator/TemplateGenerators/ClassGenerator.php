<?php declare(strict_types=1);

namespace ApiGen\Generator\TemplateGenerators;

use ApiGen\Contracts\Configuration\ConfigurationInterface;
use ApiGen\Contracts\Generator\NamedDestinationGeneratorInterface;
use ApiGen\Contracts\Generator\SourceCodeHighlighter\SourceCodeHighlighterInterface;
use ApiGen\Contracts\Parser\Elements\ElementStorageInterface;
use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Templating\TemplateFactory\TemplateFactoryInterface;
use ApiGen\Generator\Resolvers\RelativePathResolver;

final class ClassGenerator implements NamedDestinationGeneratorInterface
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
        foreach ($this->elementStorage->getClasses() as $classReflection) {
            $this->generateForClass($classReflection);
            $this->generateSourceCodeForClass($classReflection);
        }
    }

    public function getDestinationPath(string $className): string
    {
        return $this
->configuration->getDestinationWithPrefixName('class-', $className);
    }

    private function generateForClass(ClassReflectionInterface $classReflection): void
    {
        $template = $this->templateFactory->create();
        $template->setFile($this->configuration->getTemplateByName('class'));
        $template->save($this->getDestinationPath($classReflection->getName()), [
            'class' => $classReflection,
            'tree' => array_merge(array_reverse($classReflection->getParentClasses()), [$classReflection]),
        ]);
    }

    private function generateSourceCodeForClass(ClassReflectionInterface $classReflection): void
    {
        $template = $this->templateFactory->create();
        $template->setFile($this->configuration->getTemplateByName('source'));

        $content = file_get_contents($classReflection->getFileName());
        $highlightedContent = $this->sourceCodeHighlighter->highlightAndAddLineNumbers($content);

        $destination = $this->configuration->getDestinationWithPrefixName('source-class-', $classReflection->getName());

        $template->save($destination, [
            'fileName' => $this->relativePathResolver->getRelativePath($classReflection->getFileName()),
            'source' => $highlightedContent,
        ]);
    }
}
