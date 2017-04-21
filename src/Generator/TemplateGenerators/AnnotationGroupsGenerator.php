<?php declare(strict_types=1);

namespace ApiGen\Generator\TemplateGenerators;

use ApiGen\Contracts\Configuration\ConfigurationInterface;
use ApiGen\Contracts\Generator\GeneratorInterface;
use ApiGen\Contracts\Parser\Elements\ElementExtractorInterface;
use ApiGen\Contracts\Templating\TemplateRendererInterface;
use ApiGen\Parser\Elements\Elements;

final class AnnotationGroupsGenerator implements GeneratorInterface
{
    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     * @var ElementExtractorInterface
     */
    private $elementExtractor;

    /**
     * @var TemplateRendererInterface
     */
    private $templateRenderer;

    public function __construct(
        ConfigurationInterface $configuration,
        ElementExtractorInterface $elementExtractor,
        TemplateRendererInterface $templateRenderer
    ) {
        $this->configuration = $configuration;
        $this->elementExtractor = $elementExtractor;
        $this->templateRenderer = $templateRenderer;
    }

    public function generate(): void
    {
        foreach ($this->configuration->getAnnotationGroups() as $annotation) {
            $this->generateForAnnotation($annotation);
        }
    }

    private function generateForAnnotation(string $annotation): void
    {
        $elements = $this->elementExtractor->extractElementsByAnnotation($annotation);

        $this->templateRenderer->renderToFile(
            $this->configuration->getTemplateByName('annotation-group'),
            $this->configuration->getDestinationWithPrefixName('annotation-group-', $annotation),
            [
                'annotation' => $annotation,
                'hasElements' => (bool) count(array_filter($elements, 'count')),
                'classes' => $elements[Elements::CLASSES],
                'interfaces' => $elements[Elements::INTERFACES],
                'traits' => $elements[Elements::TRAITS],
                'methods' => $elements[Elements::METHODS],
                'functions' => $elements[Elements::FUNCTIONS],
                'properties' => $elements[Elements::PROPERTIES]
            ]
        );
    }
}
