<?php declare(strict_types=1);

namespace ApiGen\Templating\Filters;

use ApiGen\Configuration\ConfigurationOptions;
use ApiGen\Contracts\Configuration\ConfigurationInterface;

final class AnnotationFilters extends Filters
{
    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    public function __construct(ConfigurationInterface $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * @param string[] $annotations
     * @param string[] $annotationsToRemove
     * @return string[]
     */
    public function annotationFilter(array $annotations, array $annotationsToRemove = []): array
    {
        foreach ($annotationsToRemove as $annotationToRemove) {
            unset($annotations[$annotationToRemove]);
        }

        if (! $this->configuration->getOption(ConfigurationOptions::INTERNAL)) {
            unset($annotations['internal']);
        }

        return $annotations;
    }
}
