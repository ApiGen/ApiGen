<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Templating\Filters;

use ApiGen;
use ApiGen\Configuration\Configuration;
use ApiGen\Configuration\ConfigurationOptions as CO;
use Nette\Utils\Strings;

class AnnotationFilters extends Filters
{

    /**
     * @var array
     */
    private $rename = [
        'usedby' => 'used by'
    ];

    /**
     * @var string[]
     */
    private $remove = [
        'package', 'subpackage', 'property', 'property-read', 'property-write', 'method', 'abstract', 'access',
        'final', 'filesource', 'global', 'name', 'static', 'staticvar'
    ];

    /**
     * @var Configuration
     */
    private $configuration;


    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }


    /**
     * @param string $name
     * @return string
     */
    public function annotationBeautify($name)
    {
        if (isset($this->rename[$name])) {
            $name = $this->rename[$name];
        }
        return Strings::firstUpper($name);
    }


    /**
     * @return array
     */
    public function annotationFilter(array $annotations, array $customToRemove = [])
    {
        $annotations = $this->filterOut($annotations, $this->remove);
        $annotations = $this->filterOut($annotations, $customToRemove);

        if (! $this->configuration->getOption(CO::INTERNAL)) {
            unset($annotations['internal']);
        }

        if (! $this->configuration->getOption(CO::TODO)) {
            unset($annotations['todo']);
        }

        return $annotations;
    }


    /**
     * @deprecated since 4.2. To be removed in 5.0.
     * @return array
     */
    public function annotationSort(array $annotations)
    {
        return $annotations;
    }


    /**
     * @return array
     */
    private function filterOut(array $annotations, array $toRemove)
    {
        foreach ($toRemove as $annotation) {
            unset($annotations[$annotation]);
        }
        return $annotations;
    }
}
