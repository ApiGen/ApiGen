<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Templating\Filters;

use ApiGen;
use ApiGen\Configuration\Configuration;
use ApiGen\Configuration\ConfigurationOptions as CO;
use ApiGen\Generator\Resolvers\ElementResolver;
use Nette;


class AnnotationFilters extends Filters
{

	/**
	 * @var array
	 */
	private $rename = [
		'usedby' => 'Used by'
	];

	/**
	 * @var array
	 */
	private $remove = [
		'package',
		'subpackage',
		'property',
		'property-read',
		'property-write',
		'method',
		'abstract',
		'access',
		'final',
		'filesource',
		'global',
		'name',
		'static',
		'staticvar'
	];

	/**
	 * @var array
	 */
	private $order = [
		'deprecated' => 0,
		'category' => 1,
		'copyright' => 2,
		'license' => 3,
		'author' => 4,
		'version' => 5,
		'since' => 6,
		'see' => 7,
		'uses' => 8,
		'usedby' => 9,
		'link' => 10,
		'internal' => 11,
		'example' => 12,
		'tutorial' => 13,
		'todo' => 14
	];

	/**
	 * @var ElementResolver
	 */
	private $elementResolver;

	/**
	 * @var Configuration
	 */
	private $configuration;


	public function __construct(Configuration $configuration, ElementResolver $elementResolver)
	{
		$this->elementResolver = $elementResolver;
		$this->configuration = $configuration;
	}


	/**
	 * @param string $name
	 * @return string
	 */
	public function annotationBeautify($name)
	{
		if (isset($this->rename[$name])) {
			return $this->rename[$name];
		}

		return Nette\Utils\Strings::firstUpper($name);
	}


	/**
	 * Filter out unsupported or deprecated annotations
	 *
	 * @param array $annotations
	 * @param array $filter
	 * @return array
	 */
	public function annotationFilter(array $annotations, array $filter = [])
	{
		foreach ($this->remove as $annotation) {
			unset($annotations[$annotation]);
		}

		// Custom filter
		foreach ($filter as $annotation) {
			unset($annotations[$annotation]);
		}

		$options = $this->configuration->getOptions();

		// Show/hide internal
		if ( ! $options[CO::INTERNAL]) {
			unset($annotations['internal']);
		}

		// Show/hide tasks
		if ( ! $options[CO::TODO]) {
			unset($annotations['todo']);
		}

		return $annotations;
	}


	/**
	 * @return array
	 */
	public function annotationSort(array $annotations)
	{
		uksort($annotations, function ($one, $two) {
			if (isset($this->order[$one], $this->order[$two])) {
				return $this->order[$one] - $this->order[$two];

			} elseif (isset($this->order[$one])) {
				return -1;

			} elseif (isset($this->order[$two])) {
				return 1;

			} else {
				return strcasecmp($one, $two);
			}
		});

		return $annotations;
	}

}
