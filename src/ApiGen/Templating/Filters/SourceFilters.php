<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Templating\Filters;

use ApiGen\Configuration\Configuration;
use ApiGen\Reflection\ReflectionClass;
use ApiGen\Reflection\ReflectionConstant;
use ApiGen\Reflection\ReflectionElement;
use ApiGen\Reflection\ReflectionFunction;
use Nette;


/**
 * @method SourceFilters setConfig(array $config)
 */
class SourceFilters extends Filters
{

	/**
	 * @var array
	 */
	private $config;


	/**
	 * @param string $name
	 * @return string
	 */
	public function staticFile($name)
	{
		$versions = array();
		$filename = $this->config['destination'] . '/' . $name;
		if ( ! isset($versions[$filename]) && is_file($filename)) {
			$versions[$filename] = sprintf('%u', crc32(file_get_contents($filename)));
		}

		if (isset($versions[$filename])) {
			$name .= '?' . $versions[$filename];
		}
		return $name;
	}


	/**
	 * @param string $source
	 * @return string
	 */
	public function sourceAnchors($source)
	{
		$classInterfaceTraitMask = '~(<span\\s+class="php-keyword1">(?:class|interface|trait)</span>\\s+)(\\w+)~i';
		$source = preg_replace_callback($classInterfaceTraitMask, function ($matches) {
			$link = sprintf('<a id="%1$s" href="#%1$s">%1$s</a>', $matches[2]);
			return $matches[1] . $link;
		}, $source);

		$methodFunctionMask = '~(<span\\s+class="php-keyword1">function</span>\\s+)(\\w+)~i';
		$source = preg_replace_callback($methodFunctionMask, function ($matches) {
			$link = sprintf('<a id="_%1$s" href="#_%1$s">%1$s</a>', $matches[2]);
			return $matches[1] . $link;
		}, $source);

		$constantMask = '~(<span class="php-keyword1">const</span>)(.*?)(;)~is';
		$source = preg_replace_callback($constantMask, function ($matches) {
			$links = preg_replace_callback('~(\\s|,)([A-Z_]+)(\\s+=)~', function ($matches) {
				return $matches[1] . sprintf('<a id="%1$s" href="#%1$s">%1$s</a>', $matches[2]) . $matches[3];
			}, $matches[2]);
			return $matches[1] . $links . $matches[3];
		}, $source);

		$propertyMask = '~(<span\\s+class="php-keyword1">'
			. '(?:private|protected|public|var|static)</span>\\s+)(<span\\s+class="php-var">.*?)'
			. '(;)~is';
		$source = preg_replace_callback($propertyMask, function ($matches) {
			$links = preg_replace_callback('~(<span\\s+class="php-var">)(\\$\\w+)~i', function ($matches) {
				return $matches[1] . sprintf('<a id="%1$s" href="#%1$s">%1$s</a>', $matches[2]);
			}, $matches[2]);
			return $matches[1] . $links . $matches[3];
		}, $source);

		return $source;
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

		return sprintf($this->config['template']['templates']['source']['filename'], $file)
			. ($lines !== NULL ? '#' . $lines : '');
	}

}
