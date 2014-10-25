<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Templating\Filters;

use ApiGen\Reflection\ReflectionClass;
use ApiGen\Reflection\ReflectionConstant;
use ApiGen\Reflection\ReflectionElement;
use ApiGen\Reflection\ReflectionExtension;
use ApiGen\Reflection\ReflectionMethod;
use ApiGen\Reflection\ReflectionProperty;
use Nette;


class PhpManualFilters extends Filters
{

	/**
	 * Returns a link to a element documentation at php.net.
	 *
	 * @param ReflectionElement|ReflectionExtension|ReflectionMethod $element
	 * @return string
	 */
	public function manualUrl($element)
	{
		$manual = 'http://php.net/manual';
		$reservedClasses = array('stdClass', 'Closure', 'Directory');

		// Extension
		if ($element instanceof ReflectionExtension) {
			$extensionName = strtolower($element->getName());
			if ($extensionName === 'core') {
				return $manual;
			}

			if ($extensionName === 'date') {
				$extensionName = 'datetime';
			}

			return sprintf('%s/book.%s.php', $manual, $extensionName);
		}

		// Class and its members
		$class = $element instanceof ReflectionClass ? $element : $element->getDeclaringClass();

		if (in_array($class->getName(), $reservedClasses)) {
			return $manual . '/reserved.classes.php';
		}

		$className = strtolower($class->getName());
		$classUrl = sprintf('%s/class.%s.php', $manual, $className);
		$elementName = strtolower(strtr(ltrim($element->getName(), '_'), '_', '-'));

		if ($element instanceof ReflectionClass) {
			return $classUrl;

		} elseif ($element instanceof ReflectionMethod) {
			return sprintf('%s/%s.%s.php', $manual, $className, $elementName);

		} elseif ($element instanceof ReflectionProperty) {
			return sprintf('%s#%s.props.%s', $classUrl, $className, $elementName);

		} elseif ($element instanceof ReflectionConstant) {
			return sprintf('%s#%s.constants.%s', $classUrl, $className, $elementName);
		}

		return '';
	}

}
