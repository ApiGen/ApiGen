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
use ApiGen\Generator\Resolvers\ElementResolver;
use Nette;


/**
 * @method AnnotationFilters setConfig(array $config)
 */
class AnnotationFilters extends Filters
{

	/**
	 * @var array
	 */
	private $rename = array(
		'usedby' => 'Used by'
	);

	/**
	 * @var array
	 */
	private $remove = array(
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
	);

	/**
	 * @var array
	 */
	private $order = array(
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
	);

	/**
	 * @var array
	 */
	private $config;

	/**
	 * @var ElementResolver
	 */
	private $elementResolver;


	public function __construct(ElementResolver $elementResolver)
	{
		$this->elementResolver = $elementResolver;
	}


	/**
	 * @param string $annotation
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
	public function annotationFilter(array $annotations, array $filter = array())
	{
		foreach ($this->remove as $annotation) {
			unset($annotations[$annotation]);
		}

		// Custom filter
		foreach ($filter as $annotation) {
			unset($annotations[$annotation]);
		}

		// Show/hide internal
		if ( ! $this->config['internal']) {
			unset($annotations['internal']);
		}

		// Show/hide tasks
		if ( ! $this->config['todo']) {
			unset($annotations['todo']);
		}

		return $annotations;
	}


	/**
	 * @return array
	 */
	public function annotationSort(array $annotations)
	{
		$order = $this->order;
		uksort($annotations, function ($one, $two) use ($order) {
			if (isset($order[$one], $order[$two])) {
				return $order[$one] - $order[$two];

			} elseif (isset($order[$one])) {
				return -1;

			} elseif (isset($order[$two])) {
				return 1;

			} else {
				return strcasecmp($one, $two);
			}
		});

		return $annotations;
	}

}
