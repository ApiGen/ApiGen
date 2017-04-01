<?php declare(strict_types=1);

namespace ApiGen\Generator\TemplateGenerators;

use ApiGen\Contracts\Generator\SourceCodeHighlighter\SourceCodeHighlighterInterface;
use ApiGen\Contracts\Generator\StepCounterInterface;
use ApiGen\Contracts\Generator\TemplateGenerators\TemplateGeneratorInterface;
use ApiGen\Contracts\Parser\Elements\ElementStorageInterface;
use ApiGen\Contracts\Parser\Reflection\ElementReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\TokenReflection\ReflectionInterface;
use ApiGen\Generator\Event\GenerateProgressEvent;
use ApiGen\Generator\Resolvers\RelativePathResolver;
use ApiGen\Generator\TemplateGenerators\Loaders\NamespaceLoader;
use ApiGen\Parser\Reflection\AbstractReflection;
use ApiGen\Templating\TemplateFactory;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class SourceCodeGenerator implements TemplateGeneratorInterface, StepCounterInterface
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
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var NamespaceLoader
     */
    protected $namespaceLoader;

    public function __construct(
        ElementStorageInterface $elementStorage,
        TemplateFactory $templateFactory,
        RelativePathResolver $relativePathResolver,
        SourceCodeHighlighterInterface $sourceCodeHighlighter,
        EventDispatcherInterface $eventDispatcher,
        NamespaceLoader $namespaceLoader
    ) {
        $this->elementStorage = $elementStorage;
        $this->templateFactory = $templateFactory;
        $this->relativePathResolver = $relativePathResolver;
        $this->sourceCodeHighlighter = $sourceCodeHighlighter;
        $this->eventDispatcher = $eventDispatcher;
        $this->namespaceLoader = $namespaceLoader;
    }

    public function generate(): void
    {
        foreach ($this->elementStorage->getElements() as $type => $elementList) {
            foreach ($elementList as $element) {
                /** @var ElementReflectionInterface $element */
                if ($element->isTokenized()) {
                    $this->generateForElement($element);

                    $this->eventDispatcher->dispatch(GenerateProgressEvent::class);
                }
            }
        }
    }

    public function getStepCount(): int
    {
        $tokenizedFilter = function (ReflectionInterface $class) {
            return $class->isTokenized();
        };

        $count = count(array_filter($this->elementStorage->getClasses(), $tokenizedFilter))
            + count(array_filter($this->elementStorage->getInterfaces(), $tokenizedFilter))
            + count(array_filter($this->elementStorage->getTraits(), $tokenizedFilter))
            + count(array_filter($this->elementStorage->getExceptions(), $tokenizedFilter))
            + count($this->elementStorage->getConstants())
            + count($this->elementStorage->getFunctions());

        return $count;
    }

    /**
     * @param ElementReflectionInterface|AbstractReflection $element
     */
    private function generateForElement(ElementReflectionInterface $element): void
    {
        $template = $this->templateFactory->createNamedForElement('source', $element);
        $template = $this->namespaceLoader->loadTemplateWithElementNamespace($template, $element);
        $template->setParameters([
            'fileName' => $this->relativePathResolver->getRelativePath($element->getFileName()),
            'source' => $this->getHighlightedCodeFromElement($element)
        ]);
        $template->save();
    }

    private function getHighlightedCodeFromElement(AbstractReflection $element): string
    {
        $content = file_get_contents($element->getFileName());
        return $this->sourceCodeHighlighter->highlightAndAddLineNumbers($content);
    }
}
