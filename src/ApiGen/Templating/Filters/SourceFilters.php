<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Templating\Filters;

use ApiGen\Configuration\Configuration;
use ApiGen\Configuration\ConfigurationOptions as CO;
use ApiGen\Reflection\ReflectionClass;
use ApiGen\Reflection\ReflectionConstant;
use ApiGen\Reflection\ReflectionElement;
use ApiGen\Reflection\ReflectionFunction;
use Nette;


class SourceFilters extends Filters
{

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
	public function staticFile($name)
	{
		$filename = $this->configuration->getOption(CO::DESTINATION) . '/' . $name;
		if (is_file($filename)) {
			$name .= '?' . sha1_file($filename);
		}
		return $name;
	}


	/**
	 * Returns a link to a element source code.
	 *
	 * @param ReflectionElement $element
	 * @param bool $withLine Include file line number into the link
	 * @return string
	 */
	public function sourceUrl(ReflectionElement $element, $withLine = TRUE)
	{
		$file = '';
		if ($element instanceof ReflectionClass || $element instanceof ReflectionFunction
			|| ($element instanceof ReflectionConstant && $element->getDeclaringClassName() === NULL)
		) {
			$elementName = $element->getName();

			if ($element instanceof ReflectionClass) {
				$file = 'class-';

			} elseif ($element instanceof ReflectionConstant) {
				$file = 'constant-';

			} elseif ($element instanceof ReflectionFunction) {
				$file = 'function-';
			}

		} else {
			$elementName = $element->getDeclaringClassName();
			$file = 'class-';
		}

		$file .= $this->urlize($elementName);

		$lines = NULL;
		if ($withLine) {
			$lines = $element->getStartLine() !== $element->getEndLine()
				? sprintf('%s-%s', $element->getStartLine(), $element->getEndLine()) : $element->getStartLine();
		}

		return sprintf($this->configuration->getOption(CO::TEMPLATE)['templates']['source']['filename'], $file)
			. ($lines !== NULL ? '#' . $lines : '');
	}

}
