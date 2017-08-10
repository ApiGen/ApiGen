<?php declare(strict_types=1);

namespace ApiGen\Generator;

use ApiGen\Configuration\Configuration;
use ApiGen\Contract\Generator\GeneratorInterface;
use ApiGen\Element\ReflectionCollector\AnnotationReflectionCollector;
use ApiGen\Templating\TemplateRenderer;

final class AnnotationGroupsGenerator implements GeneratorInterface
{
    /**
     * @var Configuration
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
        Configuration $configuration,
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
        $this->templateRenderer->renderToFile(
            $this->configuration->getTemplateByName('annotation-group'),
            $this->configuration->getDestinationWithPrefixName('annotation-group-', $annotation),
            [
                'annotation' => $annotation,
                'activePage' => 'annotation-group-' . $annotation,
                'hasElements' => $this->annotationReflectionCollector->hasAnyElements(),
                'classes' => $this->annotationReflectionCollector->getClassReflections($annotation),
                'interfaces' => $this->annotationReflectionCollector->getInterfaceReflections($annotation),
                'traits' => $this->annotationReflectionCollector->getTraitReflections($annotation),
                'methods' => $this->annotationReflectionCollector->getClassOrTraitMethodReflections($annotation),
                'functions' => $this->annotationReflectionCollector->getFunctionReflections($annotation),
                'properties' => $this->annotationReflectionCollector->getClassOrTraitPropertyReflections($annotation),
            ]
        );
    }
}
