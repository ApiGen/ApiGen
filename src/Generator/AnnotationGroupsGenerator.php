<?php declare(strict_types=1);

namespace ApiGen\Generator;

use ApiGen\Contract\Configuration\ConfigurationInterface;
use ApiGen\Contract\Generator\GeneratorInterface;
use ApiGen\Element\ReflectionCollector\AnnotationReflectionCollector;
use ApiGen\Templating\TemplateRenderer;

final class AnnotationGroupsGenerator implements GeneratorInterface
{
    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     * @var TemplateRenderer
     */
    private $templateRenderer;

    /**
     * @var AnnotationReflectionCollector
     */
    private $annotationReflectionCollector;

    public function __construct(
        ConfigurationInterface $configuration,
        TemplateRenderer $templateRenderer,
        AnnotationReflectionCollector $annotationReflectionCollector
    ) {
        $this->configuration = $configuration;
        $this->templateRenderer = $templateRenderer;
        $this->annotationReflectionCollector = $annotationReflectionCollector;
    }

    public function generate(): void
    {
        foreach ($this->configuration->getAnnotationGroups() as $annotation) {
            $this->generateForAnnotation($annotation);
        }
    }

    private function generateForAnnotation(string $annotation): void
    {
        $this->annotationReflectionCollector->setActiveAnnotation($annotation);

        $this->templateRenderer->renderToFile(
            $this->configuration->getTemplateByName('annotation-group'),
            $this->configuration->getDestinationWithPrefixName('annotation-group-', $annotation),
            [
                'annotation' => $annotation,
                'hasElements' =>  $this->annotationReflectionCollector->hasAnyElements(),
                'classes' => $this->annotationReflectionCollector->getClassReflections(),
                'interfaces' => $this->annotationReflectionCollector->getInterfaceReflections(),
                'traits' => $this->annotationReflectionCollector->getTraitReflections(),
                'methods' => $this->annotationReflectionCollector->getClassOrTraitMethodReflections(),
                'functions' => $this->annotationReflectionCollector->getFunctionReflections(),
                'properties' => $this->annotationReflectionCollector->getClassOrTraitPropertyReflections()
            ]
        );
    }
}
