<?php declare(strict_types=1);

namespace ApiGen\Generator\TemplateGenerators;

use ApiGen\Contracts\Configuration\ConfigurationInterface;
use ApiGen\Contracts\Generator\NamedDestinationGeneratorInterface;
use ApiGen\Contracts\Parser\Elements\ElementExtractorInterface;
use ApiGen\Contracts\Templating\TemplateFactory\TemplateFactoryInterface;
use ApiGen\Parser\Elements\Elements;

final class AnnotationGroupsGenerator implements NamedDestinationGeneratorInterface
{
    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     * @var TemplateFactoryInterface
     */
    private $templateFactory;

    /**
     * @var ElementExtractorInterface
     */
    private $elementExtractor;

    public function __construct(
        ConfigurationInterface $configuration,
        TemplateFactoryInterface $templateFactory,
        ElementExtractorInterface $elementExtractor
    ) {
        $this->configuration = $configuration;
        $this->templateFactory = $templateFactory;
        $this->elementExtractor = $elementExtractor;
    }

    public function generate(): void
    {
        foreach ($this->configuration->getAnnotationGroups() as $annotation) {
            $this->generateForAnnotation($annotation);
        }
    }

    public function getDestinationPath(string $annotation): string
    {
        return $this->configuration->getDestinationWithPrefixName('annotation-group-', $annotation);
    }

    private function generateForAnnotation(string $annotation): void
    {
        $template = $this->templateFactory->create();

        $template->setFile(
            $this->configuration->getTemplateByName('annotation-group')
        );

        $elements = $this->elementExtractor->extractElementsByAnnotation($annotation);

        $template->save($this->getDestinationPath($annotation), [
            'annotation' => $annotation,
            'hasElements' => (bool) count(array_filter($elements, 'count')),
            'classes' => $elements[Elements::CLASSES],
            'interfaces' => $elements[Elements::INTERFACES],
            'traits' => $elements[Elements::TRAITS],
            'methods' => $elements[Elements::METHODS],
            'functions' => $elements[Elements::FUNCTIONS],
            'properties' => $elements[Elements::PROPERTIES]
        ]);
    }
}
