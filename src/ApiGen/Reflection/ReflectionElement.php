<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Reflection;

use TokenReflection\Exception\BaseException;
use TokenReflection\ReflectionAnnotation;


/**
 * Element reflection envelope.
 * Alters TokenReflection\IReflection functionality for ApiGen.
 */
abstract class ReflectionElement extends ReflectionBase
{

	/**
	 * Cache for information if the element should be documented.
	 *
	 * @var bool
	 */
	protected $isDocumented;

	/**
	 * Reflection elements annotations.
	 *
	 * @var array
	 */
	protected $annotations;

	/**
	 * Reasons why this element's reflection is invalid.
	 *
	 * @var array
	 */
	private $reasons = array();


	/**
	 * Returns the PHP extension reflection.
	 *
	 * @return ReflectionExtension|NULL
	 */
	public function getExtension()
	{
		$extension = $this->reflection->getExtension();
		return $extension === NULL ? NULL : new ReflectionExtension($extension);
	}


	/**
	 * @return bool
	 */
	public function getExtensionName()
	{
		return $this->reflection->getExtensionName();
	}


	/**
	 * @return integer
	 */
	public function getStartPosition()
	{
		return $this->reflection->getStartPosition();
	}


	/**
	 * @return integer
	 */
	public function getEndPosition()
	{
		return $this->reflection->getEndPosition();
	}


	/**
	 * Returns if the element belongs to main project.
	 *
	 * @return bool
	 */
	public function isMain()
	{
		return empty(self::$config->main) || strpos($this->getName(), self::$config->main) === 0;
	}


	/**
	 * Returns if the element should be documented.
	 *
	 * @return bool
	 */
	public function isDocumented()
	{
		if ($this->isDocumented === NULL) {
			$this->isDocumented = $this->reflection->isTokenized() || $this->reflection->isInternal();

			if ($this->isDocumented) {
				if ( ! self::$config->php && $this->reflection->isInternal()) {
					$this->isDocumented = FALSE;

				} elseif ( ! self::$config->deprecated && $this->reflection->isDeprecated()) {
					$this->isDocumented = FALSE;

				} elseif ( ! self::$config->internal && ($internal = $this->reflection->getAnnotation('internal'))
					&& empty($internal[0])
				) {
					$this->isDocumented = FALSE;

				} elseif (count($this->reflection->getAnnotation('ignore')) > 0) {
					$this->isDocumented = FALSE;
				}
			}
		}

		return $this->isDocumented;
	}


	/**
	 * Returns if the element is deprecated.
	 *
	 * @return bool
	 */
	public function isDeprecated()
	{
		if ($this->reflection->isDeprecated()) {
			return TRUE;
		}

		if (($this instanceof ReflectionMethod || $this instanceof ReflectionProperty || $this instanceof ReflectionConstant)
			&& $class = $this->getDeclaringClass()
		) {
			return $class->isDeprecated();
		}

		return FALSE;
	}


	/**
	 * Returns if the element is in package.
	 *
	 * @return bool
	 */
	public function inPackage()
	{
		return ($this->getPackageName() !== '');
	}


	/**
	 * Returns element package name (including subpackage name).
	 *
	 * @return string
	 */
	public function getPackageName()
	{
		static $packages = array();

		if ($package = $this->getAnnotation('package')) {
			$packageName = preg_replace('~\s+.*~s', '', $package[0]);
			if (empty($packageName)) {
				return '';
			}

			if ($subpackage = $this->getAnnotation('subpackage')) {
				$subpackageName = preg_replace('~\s+.*~s', '', $subpackage[0]);
				if ( ! empty($subpackageName) && strpos($subpackageName, $packageName) === 0) {
					$packageName = $subpackageName;

				} else {
					$packageName .= '\\' . $subpackageName;
				}
			}
			$packageName = strtr($packageName, '._/', '\\\\\\');

			$lowerPackageName = strtolower($packageName);
			if ( ! isset($packages[$lowerPackageName])) {
				$packages[$lowerPackageName] = $packageName;
			}

			return $packages[$lowerPackageName];
		}

		return '';
	}


	/**
	 * Returns element package name (including subpackage name).
	 * For internal elements returns "PHP", for elements in global space returns "None".
	 *
	 * @return string
	 */
	public function getPseudoPackageName()
	{
		if ($this->isInternal()) {
			return 'PHP';
		}

		return $this->getPackageName() ?: 'None';
	}


	/**
	 * Returns if the element is defined within a namespace.
	 *
	 * @return bool
	 */
	public function inNamespace()
	{
		return $this->getNamespaceName() !== '';
	}


	/**
	 * Returns element namespace name.
	 *
	 * @return string
	 */
	public function getNamespaceName()
	{
		static $namespaces = array();

		$namespaceName = $this->reflection->getNamespaceName();

		if ( ! $namespaceName) {
			return $namespaceName;
		}

		$lowerNamespaceName = strtolower($namespaceName);
		if ( ! isset($namespaces[$lowerNamespaceName])) {
			$namespaces[$lowerNamespaceName] = $namespaceName;
		}

		return $namespaces[$lowerNamespaceName];
	}


	/**
	 * Returns element namespace name.
	 * For internal elements returns "PHP", for elements in global space returns "None".
	 *
	 * @return string
	 */
	public function getPseudoNamespaceName()
	{
		return $this->isInternal() ? 'PHP' : $this->getNamespaceName() ?: 'None';
	}


	/**
	 * Returns imported namespaces and aliases from the declaring namespace.
	 *
	 * @return array
	 */
	public function getNamespaceAliases()
	{
		return $this->reflection->getNamespaceAliases();
	}


	/**
	 * Returns the short description.
	 *
	 * @return string
	 */
	public function getShortDescription()
	{
		$short = $this->reflection->getAnnotation(ReflectionAnnotation::SHORT_DESCRIPTION);
		if ( ! empty($short)) {
			return $short;
		}

		if ($this instanceof ReflectionProperty || $this instanceof ReflectionConstant) {
			$var = $this->getAnnotation('var');
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
		$long = $this->reflection->getAnnotation(ReflectionAnnotation::LONG_DESCRIPTION);

		if ( ! empty($long)) {
			$short .= "\n\n" . $long;
		}

		return $short;
	}


	/**
	 * Returns the appropriate docblock definition.
	 *
	 * @return string|boolean
	 */
	public function getDocComment()
	{
		return $this->reflection->getDocComment();
	}


	/**
	 * Returns reflection element annotations.
	 * Removes the short and long description.
	 * In case of classes, functions and constants, @package, @subpackage, @author and @license annotations
	 * are added from declaring files if not already present.
	 *
	 * @return array
	 */
	public function getAnnotations()
	{
		if ($this->annotations === NULL) {
			static $fileLevel = array(
				'package' => TRUE,
				'subpackage' => TRUE,
				'author' => TRUE,
				'license' => TRUE,
				'copyright' => TRUE
			);

			$annotations = $this->reflection->getAnnotations();
			$annotations = array_change_key_case($annotations, CASE_LOWER);

			unset($annotations[ReflectionAnnotation::SHORT_DESCRIPTION]);
			unset($annotations[ReflectionAnnotation::LONG_DESCRIPTION]);

			// @todo: fix
			if ($this->reflection instanceof \TokenReflectionClass
				|| $this->reflection instanceof \TokenReflectionFunction
				|| ($this->reflection instanceof \TokenReflectionConstant
				&& $this->reflection->getDeclaringClassName() === NULL)
			) {
				foreach ($this->reflection->getFileReflection()->getAnnotations() as $name => $value) {
					if (isset($fileLevel[$name]) && empty($annotations[$name])) {
						$annotations[$name] = $value;
					}
				}
			}

			$this->annotations = $annotations;
		}

		return $this->annotations;
	}


	/**
	 * Returns reflection element annotation.
	 *
	 * @param string $annotation
	 * @return array
	 */
	public function getAnnotation($annotation)
	{
		$annotations = $this->getAnnotations();
		return isset($annotations[$annotation]) ? $annotations[$annotation] : NULL;
	}


	/**
	 * Checks if there is a particular annotation.
	 *
	 * @param string $annotation
	 * @return bool
	 */
	public function hasAnnotation($annotation)
	{
		$annotations = $this->getAnnotations();
		return isset($annotations[$annotation]);
	}


	/**
	 * Adds element annotation.
	 *
	 * @param string $annotation
	 * @param string $value
	 * @return ReflectionElement
	 */
	public function addAnnotation($annotation, $value)
	{
		if ($this->annotations === NULL) {
			$this->getAnnotations();
		}
		$this->annotations[$annotation][] = $value;

		return $this;
	}


	/**
	 * Adds a reason why this element's reflection is invalid.
	 *
	 * @param \TokenReflection\Exception\BaseException $reason Reason
	 * @return \TokenReflection\Invalid\ReflectionElement
	 */
	public function addReason(BaseException $reason)
	{
		$this->reasons[] = $reason;

		return $this;
	}


	/**
	 * Returns a list of reasons why this element's reflection is invalid.
	 *
	 * @return array
	 */
	public function getReasons()
	{
		return $this->reasons;
	}


	/**
	 * Returns if there are any known reasons why this element's reflection is invalid.
	 *
	 * @return bool
	 */
	public function hasReasons()
	{
		return ! empty($this->reasons);
	}

}
