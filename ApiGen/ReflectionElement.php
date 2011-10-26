<?php

/**
 * ApiGen 2.2.1 - API documentation generator for PHP 5.3+
 *
 * Copyright (c) 2010 David Grudl (http://davidgrudl.com)
 * Copyright (c) 2011 Jaroslav Hanslík (https://github.com/kukulich)
 * Copyright (c) 2011 Ondřej Nešpor (https://github.com/Andrewsville)
 *
 * For the full copyright and license information, please view
 * the file LICENSE.md that was distributed with this source code.
 */

namespace ApiGen;

/**
 * Element reflection envelope.
 *
 * Alters TokenReflection\IReflection functionality for ApiGen.
 *
 * @author Jaroslav Hanslík
 * @author Ondřej Nešpor
 */
abstract class ReflectionElement extends ReflectionBase
{
	/**
	 * Cache for information if the element should be documented.
	 *
	 * @var boolean
	 */
	protected $isDocumented;

	/**
	 * Returns the PHP extension reflection.
	 *
	 * @return \ApiGen\ReflectionExtension
	 */
	public function getExtension()
	{
		$extension = $this->reflection->getExtension();
		return null === $extension ? null : new ReflectionExtension($extension, self::$generator);
	}

	/**
	 * Returns if the element belongs to main project.
	 *
	 * @return boolean
	 */
	public function isMain()
	{
		return empty(self::$config->main) || 0 === strpos($this->reflection->getName(), self::$config->main);
	}

	/**
	 * Returns if the element should be documented.
	 *
	 * @return boolean
	 */
	public function isDocumented()
	{
		if (null === $this->isDocumented) {
			$this->isDocumented = $this->reflection->isTokenized() || $this->reflection->isInternal();

			if ($this->isDocumented) {
				if (!self::$config->php && $this->reflection->isInternal()) {
					$this->isDocumented = false;
				} elseif (!self::$config->deprecated && $this->reflection->isDeprecated()) {
					$this->isDocumented = false;
				} elseif (!self::$config->internal && ($internal = $this->reflection->getAnnotation('internal')) && empty($internal[0])) {
					$this->isDocumented = false;
				}
			}
		}

		return $this->isDocumented;
	}

	/**
	 * Returns element package name (including subpackage name).
	 *
	 * For internal elements returns "PHP", for elements in global space returns "None".
	 *
	 * @return string
	 */
	public function getPseudoPackageName()
	{
		if ($this->reflection->isInternal()) {
			return 'PHP';
		}

		if ($package = $this->reflection->getAnnotation('package')) {
			$packageName = preg_replace('~\s+.*~s', '', $package[0]);
			if ($subpackage = $this->reflection->getAnnotation('subpackage')) {
				$packageName .= '\\' . preg_replace('~\s+.*~s', '', $subpackage[0]);
			}
			return $packageName;
		}

		return 'None';
	}

	/**
	 * Returns element namespace name.
	 *
	 * For internal elements returns "PHP", for elements in global space returns "None".
	 *
	 * @return string
	 */
	public function getPseudoNamespaceName()
	{
		return $this->reflection->isInternal() ? 'PHP' : $this->reflection->getNamespaceName() ?: 'None';
	}

	/**
	 * Returns the short description.
	 *
	 * @return string
	 */
	public function getShortDescription()
	{
		$short = $this->reflection->getAnnotation(\TokenReflection\ReflectionAnnotation::SHORT_DESCRIPTION);
		if (!empty($short)) {
			return $short;
		}

		if ($this instanceof ReflectionProperty || $this instanceof ReflectionConstant) {
			$var = $this->reflection->getAnnotation('var');
			list(, $short) = preg_split('~\s+|$~', $var[0], 2);
		}

		return $short;
	}

	/**
	 * Returns the long description.
	 *
	 * @return string
	 */
	public function getLongDescription()
	{
		$short = $this->getShortDescription();
		$long = $this->reflection->getAnnotation(\TokenReflection\ReflectionAnnotation::LONG_DESCRIPTION);

		if (!empty($long)) {
			$short .= "\n\n" . $long;
		}

		return $short;
	}

	/**
	 * Returns all annotations.
	 *
	 * @return array
	 */
	public function getAnnotations()
	{
		$annotations = $this->reflection->getAnnotations();
		unset($annotations[\TokenReflection\ReflectionAnnotation::SHORT_DESCRIPTION]);
		unset($annotations[\TokenReflection\ReflectionAnnotation::LONG_DESCRIPTION]);
		return $annotations;
	}
}
