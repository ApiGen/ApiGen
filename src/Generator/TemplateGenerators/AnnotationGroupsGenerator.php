<?php declare(strict_types=1);

namespace ApiGen\Generator\TemplateGenerators;

use ApiGen\Contracts\Configuration\ConfigurationInterface;
use ApiGen\Contracts\Generator\TemplateGenerators\TemplateGeneratorInterface;
use ApiGen\Contracts\Parser\Elements\ElementExtractorInterface;
use ApiGen\Contracts\Templating\TemplateFactory\TemplateFactoryInterface;
use ApiGen\Parser\Elements\Elements;
use ApiGen\Templating\Filters\Filters;
use ApiGen\Templating\Template;

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

        $template->setSavePath(
            $this->configuration->getDestination()
            . DIRECTORY_SEPARATOR
            . sprintf('annotation-group-%s.html', Filters::urlize($annotation))
        );

        $this->setElementsWithAnnotationToTemplate($template, $annotation);
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
