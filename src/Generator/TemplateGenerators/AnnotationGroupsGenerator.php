<?php declare(strict_types=1);

namespace ApiGen\Generator\TemplateGenerators;

use ApiGen\Contracts\Configuration\ConfigurationInterface;
use ApiGen\Contracts\Generator\TemplateGenerators\TemplateGeneratorInterface;
use ApiGen\Contracts\Parser\Elements\ElementExtractorInterface;
use ApiGen\Parser\Elements\Elements;
use ApiGen\Templating\Template;
use ApiGen\Templating\TemplateFactory;

final class AnnotationGroupsGenerator implements TemplateGeneratorInterface
{
    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     * @var TemplateFactory
     */
    private $templateFactory;

    /**
     * @var ElementExtractorInterface
     */
    private $elementExtractor;

    public function __construct(
        ConfigurationInterface $configuration,
        TemplateFactory $templateFactory,
        ElementExtractorInterface $elementExtractor
    ) {
        $this->configuration = $configuration;
        $this->templateFactory = $templateFactory;
        $this->elementExtractor = $elementExtractor;
    }

    public function generate(): void
    {
        $annotationGroups = $this->configuration->getAnnotationGroups();

        foreach ($annotationGroups as $annotationGroup) {
            $this->generateForAnnotation($annotationGroup);
        }
    }

    private function generateForAnnotation(string $annotationGroup): void
    {
        $template = $this->templateFactory->createNamedForElement(
            TemplateFactory::ELEMENT_ANNOTATION_GROUP,
            $annotationGroup
        );

        $this->setElementsWithAnnotationToTemplate($template, $annotationGroup);
        $template->save();
    }

    private function setElementsWithAnnotationToTemplate(Template $template, string $annotation): void
    {
        $elements = $this->elementExtractor->extractElementsByAnnotation($annotation);

        $template->setParameters([
            'annotation' => $annotation,
            'hasElements' => (bool) count(array_filter($elements, 'count')),
            Elements::CLASSES => $elements[Elements::CLASSES],
            Elements::INTERFACES => $elements[Elements::INTERFACES],
            Elements::TRAITS => $elements[Elements::TRAITS],
            Elements::METHODS => $elements[Elements::METHODS],
            Elements::FUNCTIONS => $elements[Elements::FUNCTIONS],
            Elements::PROPERTIES => $elements[Elements::PROPERTIES]
        ]);
    }
}
