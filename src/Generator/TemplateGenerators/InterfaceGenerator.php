<?php declare(strict_types=1);

namespace ApiGen\Generator\TemplateGenerators;

use ApiGen\Contracts\Configuration\ConfigurationInterface;
use ApiGen\Contracts\Generator\NamedDestinationGeneratorInterface;
use ApiGen\Contracts\Generator\SourceCodeHighlighter\SourceCodeHighlighterInterface;
use ApiGen\Contracts\Parser\Elements\ElementStorageInterface;
use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Templating\TemplateFactory\TemplateFactoryInterface;
use ApiGen\Generator\Resolvers\RelativePathResolver;

final class InterfaceGenerator implements NamedDestinationGeneratorInterface
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
        foreach ($this->elementStorage->getInterfaces() as $interfaceReflection) {
            $this->generateForInterface($interfaceReflection);
            $this->generateSourceCodeForInterface($interfaceReflection);
        }
    }

    public function getDestinationPath(string $interfaceName): string
    {
        return $this->configuration->getDestinationWithPrefixName('interface-', $interfaceName);
    }

    private function generateForInterface(ClassReflectionInterface $interfaceReflection): void
    {
        $template = $this->templateFactory->create();
        $template->setFile($this->configuration->getTemplateByName('interface'));

        $template->save($this->getDestinationPath($interfaceReflection->getName()), [
            'interface' => $interfaceReflection,
            'tree' => array_merge(array_reverse($interfaceReflection->getParentClasses()), [$interfaceReflection]),
        ]);
    }

    private function generateSourceCodeForInterface(ClassReflectionInterface $interfaceReflection)
    {
        $template = $this->templateFactory->create();
        $template->setFile($this->configuration->getTemplateByName('source'));

        $content = file_get_contents($interfaceReflection->getFileName());
        $highlightedContent = $this->sourceCodeHighlighter->highlightAndAddLineNumbers($content);

        $destination = $this->configuration->getDestinationWithPrefixName('source-interface-', $interfaceReflection->getName());

        $template->save($destination, [
            'fileName' => $this->relativePathResolver->getRelativePath($interfaceReflection->getFileName()),
            'source' => $highlightedContent,
        ]);
    }
}
