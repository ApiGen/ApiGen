<?php declare(strict_types=1);

namespace ApiGen\Generator\TemplateGenerators;

use ApiGen\Contracts\Configuration\ConfigurationInterface;
use ApiGen\Contracts\Generator\TemplateGenerators\TemplateGeneratorInterface;
use ApiGen\Contracts\Parser\Elements\ElementExtractorInterface;
use ApiGen\Contracts\Templating\TemplateFactory\TemplateFactoryInterface;
use ApiGen\Parser\Elements\Elements;
use ApiGen\Templating\Filters\Filters;

final class AnnotationGroupsGenerator implements TemplateGeneratorInterface
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

    private function generateForAnnotation(string $annotation): void
    {
        $template = $this->templateFactory->create();

        $template->setFile(
            $this->configuration->getTemplatesDirectory() . DIRECTORY_SEPARATOR . 'annotation-group.latte'
        );

        $elements = $this->elementExtractor->extractElementsByAnnotation($annotation);

        $template->save($this->getDestination($annotation), [
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

    private function getDestination(string $annotation): string
    {
        return $this->configuration->getDestination()
            . DIRECTORY_SEPARATOR
            . sprintf(
                'annotation-group-%s.html',
                Filters::urlize($annotation)
            );
    }
}
