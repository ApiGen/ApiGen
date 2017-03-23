<?php declare(strict_types=1);

namespace ApiGen\Templating\Filters;

use ApiGen\Configuration\ConfigurationOptions;
use ApiGen\Contracts\Configuration\ConfigurationInterface;
use Nette\Utils\Strings;

final class AnnotationFilters extends Filters
{
    /**
     * @var string[]
     */
    private $remove = [
        'package', 'subpackage', 'property', 'property-read', 'property-write', 'method', 'abstract', 'access',
        'final', 'filesource', 'global', 'name', 'static', 'staticvar'
    ];

    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    public function __construct(ConfigurationInterface $configuration)
    {
        $this->configuration = $configuration;
    }

    public function annotationBeautify(string $name): string
    {
        return Strings::firstUpper($name);
    }

    /**
     * @param string[] $annotations
     * @param string[] $customToRemove
     * @return string[]
     */
    public function annotationFilter(array $annotations, array $customToRemove = []): array
    {
        $annotations = $this->filterOut($annotations, $this->remove);
        $annotations = $this->filterOut($annotations, $customToRemove);

        if (! $this->configuration->getOption(ConfigurationOptions::INTERNAL)) {
            unset($annotations['internal']);
        }

        return $annotations;
    }

    /**
     * @param string[] $annotations
     * @param string[] $toRemove
     * @return string[]
     */
    private function filterOut(array $annotations, array $toRemove): array
    {
        foreach ($toRemove as $annotation) {
            unset($annotations[$annotation]);
        }

        return $annotations;
    }
}
