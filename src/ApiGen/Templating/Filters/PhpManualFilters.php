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

	const PHP_MANUAL_URL = 'http://php.net/manual';


	/**
	 * Returns a link to a element documentation at php.net.
	 *
	 * @param ReflectionElement|ReflectionExtension|ReflectionMethod $element
	 * @return string
	 */
	public function manualUrl($element)
	{
		if ($element instanceof ReflectionExtension) {
			return $this->createExtensionUrl($element);
		}

		// Class and its members
		$class = $element instanceof ReflectionClass ? $element : $element->getDeclaringClass();

		$reservedClasses = ['stdClass', 'Closure', 'Directory'];
		if (in_array($class->getName(), $reservedClasses)) {
			return self::PHP_MANUAL_URL . '/reserved.classes.php';
		}

		$className = strtolower($class->getName());
		$classUrl = sprintf('%s/class.%s.php', self::PHP_MANUAL_URL, $className);
		$elementName = strtolower(strtr(ltrim($element->getName(), '_'), '_', '-'));

		if ($element instanceof ReflectionClass) {
			return $classUrl;

		} elseif ($element instanceof ReflectionMethod) {
			return sprintf('%s/%s.%s.php', self::PHP_MANUAL_URL, $className, $elementName);

		} elseif ($element instanceof ReflectionProperty) {
			return sprintf('%s#%s.props.%s', $classUrl, $className, $elementName);

		} elseif ($element instanceof ReflectionConstant) {
			return sprintf('%s#%s.constants.%s', $classUrl, $className, $elementName);
		}

		return '';
	}


	/**
	 * @return string
	 */
	private function createExtensionUrl(ReflectionExtension $element)
	{
		$extensionName = strtolower($element->getName());
		if ($extensionName === 'core') {
			return self::PHP_MANUAL_URL;
		}

		if ($extensionName === 'date') {
			$extensionName = 'datetime';
		}

		return sprintf('%s/book.%s.php', self::PHP_MANUAL_URL, $extensionName);
	}

}
