<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Templating\Filters;

use ApiGen\Configuration\Configuration;
use ApiGen\Configuration\TemplateConfiguration;
use ApiGen\Generator\Resolvers\ElementResolver;
use ApiGen\Reflection\ReflectionClass;
use ApiGen\Reflection\ReflectionFunction;
use ApiGen\Templating\Filters\Helpers\Strings;
use ApiGen\Generator\Markups\Markup;
use ApiGen\Generator\Highlighter\SourceCodeHighlighter;
use ApiGen\Reflection\ReflectionConstant;
use ApiGen\Reflection\ReflectionElement;
use ApiGen\Reflection\ReflectionMethod;
use ApiGen\Reflection\ReflectionProperty;
use Nette\Utils\Validators;


class UrlFilters extends Filters
{

	/**
	 * @var SourceCodeHighlighter
	 */
	private $highlighter;

	/**
	 * @var Markup
	 */
	private $markup;

	/**
	 * @var ElementResolver
	 */
	private $elementResolver;

	/**
	 * @var Configuration
	 */
	private $configuration;


	public function __construct(
		SourceCodeHighlighter $highlighter,
		Markup $markup,
		ElementResolver $elementResolver,
		Configuration $configuration
	) {
		$this->highlighter = $highlighter;
		$this->markup = $markup;
		$this->elementResolver = $elementResolver;
		$this->configuration = $configuration;
	}


	/**
	 * Returns a link to element summary file.
	 *
	 * @return string
	 */
	public function elementUrl(ReflectionElement $element)
	{
		if ($element instanceof ReflectionClass) {
			return $this->classUrl($element);

		} elseif ($element instanceof ReflectionMethod) {
			return $this->methodUrl($element);

		} elseif ($element instanceof ReflectionProperty) {
			return $this->propertyUrl($element);

		} elseif ($element instanceof ReflectionConstant) {
			return $this->constantUrl($element);

		} elseif ($element instanceof ReflectionFunction) {
			return $this->functionUrl($element);
		}

		return NULL;
	}


	/**
	 * Returns links for package/namespace and its parent packages.
	 *
	 * @param string $package
	 * @param boolean $last
	 * @return string
	 */
	public function packageLinks($package, $last = TRUE)
	{
		if (empty($this->packages)) {
			return $package;
		}

		$links = array();

		$parent = '';
		foreach (explode('\\', $package) as $part) {
			$parent = ltrim($parent . '\\' . $part, '\\');
			$links[] = $last || $parent !== $package
				? $this->link($this->packageUrl($parent), $part)
				: $this->escapeHtml($part);
		}

		return implode('\\', $links);
	}


	/**
	 * @param string $groupName
	 * @return string
	 */
	public function subgroupName($groupName)
	{
		if ($pos = strrpos($groupName, '\\')) {
			return substr($groupName, $pos + 1);
		}
		return $groupName;
	}


	/**
	 * Returns links for namespace and its parent namespaces.
	 *
	 * @param string $namespace
	 * @param boolean $last
	 * @return string
	 */
	public function namespaceLinks($namespace, $last = TRUE)
	{
		if (empty($this->namespaces)) {
			return $namespace;
		}

		$links = array();

		$parent = '';
		foreach (explode('\\', $namespace) as $part) {
			$parent = ltrim($parent . '\\' . $part, '\\');
			$links[] = $last || $parent !== $namespace
				? $this->link($this->namespaceUrl($parent), $part)
				: $this->escapeHtml($part);
		}

		return implode('\\', $links);
	}


	/**
	 * Returns a link to a package summary file.
	 *
	 * @param string $packageName Package name
	 * @return string
	 */
	public function packageUrl($packageName)
	{
		$options = $this->configuration->getOptions();
		return sprintf(
			$options['template']['templates']['package']['filename'],
			$this->urlize($packageName)
		);
	}


	/**
	 * Returns a link to a namespace summary file.
	 *
	 * @param string $namespaceName Namespace name
	 * @return string
	 */
	public function namespaceUrl($namespaceName)
	{
		$options = $this->configuration->getOptions();
		return sprintf(
			$options['template']['templates']['namespace']['filename'],
			$this->urlize($namespaceName)
		);
	}


	/**
	 * Returns a link to class summary file.
	 *
	 * @param string|ReflectionClass $class Class reflection or name
	 * @return string
	 */
	public function classUrl($class)
	{
		$className = $class instanceof ReflectionClass ? $class->getName() : $class;
		$options = $this->configuration->getOptions();
		return sprintf(
			$options['template']['templates']['class']['filename'],
			$this->urlize($className)
		);
	}


	/**
	 * Returns a link to method in class summary file.
	 *
	 * @return string
	 */
	public function methodUrl(ReflectionMethod $method, ReflectionClass $class = NULL)
	{
		$className = $class !== NULL ? $class->getName() : $method->getDeclaringClassName();
		return $this->classUrl($className) . '#' . ($method->isMagic() ? 'm' : '') . '_'
			. ($method->getOriginalName() ?: $method->getName());
	}


	/**
	 * Returns a link to property in class summary file.
	 *
	 * @return string
	 */
	public function propertyUrl(ReflectionProperty $property, ReflectionClass $class = NULL)
	{
		$className = $class !== NULL ? $class->getName() : $property->getDeclaringClassName();
		return $this->classUrl($className) . '#' . ($property->isMagic() ? 'm' : '') . '$' . $property->getName();
	}


	/**
	 * Returns a link to constant in class summary file or to constant summary file.
	 *
	 * @return string
	 */
	public function constantUrl(ReflectionConstant $constant)
	{
		// Class constant
		if ($className = $constant->getDeclaringClassName()) {
			return $this->classUrl($className) . '#' . $constant->getName();
		}
		// Constant in namespace or global space
		$options = $this->configuration->getOptions();
		return sprintf(
			$options['template']['templates']['constant']['filename'],
			$this->urlize($constant->getName())
		);
	}


	/**
	 * Returns a link to function summary file.
	 *
	 * @return string
	 */
	public function functionUrl(ReflectionFunction $function)
	{
		$options = $this->configuration->getOptions();
		return sprintf(
			$options['template']['templates']['function']['filename'],
			$this->urlize($function->getName())
		);
	}


	/**
	 * Tries to parse a definition of a class/method/property/constant/function
	 * and returns the appropriate link if successful.
	 *
	 * @param string $definition
	 * @param ReflectionElement $context
	 * @return string|NULL
	 */
	public function resolveLink($definition, ReflectionElement $context)
	{
		if (empty($definition)) {
			return NULL;
		}

		$suffix = '';
		if (substr($definition, -2) === '[]') {
			$definition = substr($definition, 0, -2);
			$suffix = '[]';
		}

		$element = $this->elementResolver->resolveElement($definition, $context, $expectedName);
		if ($element === NULL) {
			return $expectedName;
		}

		$classes = array();
		if ($element->isDeprecated()) {
			$classes[] = 'deprecated';
		}

		/** @var ReflectionClass|ReflectionConstant $element */
		if ( ! $element->isValid()) {
			$classes[] = 'invalid';
		}

		if ($element instanceof ReflectionClass) {
			$link = $this->link($this->classUrl($element), $element->getName(), TRUE, $classes);

		} elseif ($element instanceof ReflectionConstant && $element->getDeclaringClassName() === NULL) {
			$text = $element->inNamespace()
				? $this->escapeHtml($element->getNamespaceName()) . '\\<b>' . $this->escapeHtml($element->getShortName()) . '</b>'
				: '<b>' . $this->escapeHtml($element->getName()) . '</b>';
			$link = $this->link($this->constantUrl($element), $text, FALSE, $classes);

		} elseif ($element instanceof ReflectionFunction) {
			$link = $this->link($this->functionUrl($element), $element->getName() . '()', TRUE, $classes);

		} else {
			$url = '';
			$text = $this->escapeHtml($element->getDeclaringClassName());
			if ($element instanceof ReflectionProperty) {
				$url = $this->propertyUrl($element);
				$text .= '::<var>$' . $this->escapeHtml($element->getName()) . '</var>';

			} elseif ($element instanceof ReflectionMethod) {
				$url = $this->methodUrl($element);
				$text .= '::' . $this->escapeHtml($element->getName()) . '()';

			} elseif ($element instanceof ReflectionConstant) {
				$url = $this->constantUrl($element);
				$text .= '::<b>' . $this->escapeHtml($element->getName()) . '</b>';
			}

			$link = $this->link($url, $text, FALSE, $classes);
		}

		return sprintf('<code>%s</code>', $link . $suffix);
	}


	/**
	 * Returns links for package/namespace and its parent packages.
	 *
	 * @param string $package
	 * @param boolean $last
	 * @return string
	 */
	public function getPackageLinks($package, $last = TRUE)
	{
		if (empty($this->packages)) {
			return $package;
		}

		$links = array();

		$parent = '';
		foreach (explode('\\', $package) as $part) {
			$parent = ltrim($parent . '\\' . $part, '\\');
			$links[] = $last || $parent !== $package
				? $this->link($this->packageUrl($parent), $part)
				: $this->escapeHtml($part);
		}

		return implode('\\', $links);
	}


	/**
	 * Individual annotations processing
	 *
	 * @param string $value
	 * @param string $name
	 * @param ReflectionElement $context
	 * @return string
	 */
	public function annotation($value, $name, ReflectionElement $context)
	{
		switch ($name) {
			case 'return':
			case 'throws':
				$description = trim(strpbrk($value, "\n\r\t $")) ?: NULL;
				if ($description) {
					$description = '<br>' . $this->doc($description, $context);
				}
				$typeLinks = $this->typeLinks($value, $context);

				if ($name === 'throws') {
					return $typeLinks . $description;

				} else {
					return sprintf('<code>%s</code>%s', $typeLinks, $description);
				}

			case 'license':
				list($url, $description) = Strings::split($value);
				return Strings::link($url, $description ?: $url);

			case 'link':
				list($url, $description) = Strings::split($value);
				if (Validators::isUri($url)) {
					return Strings::link($url, $description ?: $url);
				}
				break;

			case 'see':
				$doc = array();
				foreach (preg_split('~\\s*,\\s*~', $value) as $link) {
					if ($this->elementResolver->resolveElement($link, $context) !== NULL) {
						$doc[] = sprintf('<code>%s</code>', $this->typeLinks($link, $context));

					} else {
						$doc[] = $this->doc($link, $context);
					}
				}
				return implode(', ', $doc);

			case 'uses':
			case 'usedby':
				list($link, $description) = Strings::split($value);
				$separator = $context instanceof ReflectionClass || ! $description ? ' ' : '<br>';
				if ($this->elementResolver->resolveElement($link, $context) !== NULL) {
					return sprintf('<code>%s</code>%s%s', $this->typeLinks($link, $context), $separator, $description);
				}
				break;
			default:
				return $this->doc($value, $context);
				break;
		}
	}


	/**
	 * Returns links for types.
	 *
	 * @param string $annotation
	 * @param ReflectionElement $context
	 * @return string
	 */
	public function typeLinks($annotation, ReflectionElement $context)
	{
		$links = array();

		list($types) = Strings::split($annotation);
		if ( ! empty($types) && $types{0} === '$') {
			$types = NULL;
		}

		if (empty($types)) {
			$types = 'mixed';
		}

		foreach (explode('|', $types) as $type) {
			$type = $this->getTypeName($type, FALSE);
			$links[] = $this->resolveLink($type, $context) ?: $this->escapeHtml(ltrim($type, '\\'));
		}

		return implode('|', $links);
	}


	/********************* description *********************/

	/**
	 * Docblock description
	 */
	public function description($annotation, $context)
	{
		$description = trim(strpbrk($annotation, "\n\r\t $")) ?: $annotation;
		return $this->doc($description, $context);
	}


	/**
	 * @param ReflectionElement $element
	 * @param bool $block
	 * @return mixed
	 */
	public function shortDescription($element, $block = FALSE)
	{
		return $this->doc($element->getShortDescription(), $element, $block);
	}


	/**
	 * @param ReflectionElement $element
	 * @return mixed
	 */
	public function longDescription($element)
	{
		$long = $element->getLongDescription();

		// Merge lines
		$long = preg_replace_callback('~(?:<(code|pre)>.+?</\1>)|([^<]*)~s', function ($matches) {
			return ! empty($matches[2])
				? preg_replace('~\n(?:\t|[ ])+~', ' ', $matches[2])
				: $matches[0];
		}, $long);

		return $this->doc($long, $element, TRUE);
	}


	/********************* text formatter *********************/


	/**
	 * Formats text as documentation block or line.
	 *
	 * @param string $text
	 * @param ReflectionElement $context
	 * @param boolean $block Parse text as block
	 * @return string
	 */
	public function doc($text, ReflectionElement $context, $block = FALSE)
	{
		// Resolve @internal
		$text = $this->resolveInternal($text);

		// Process markup
		if ($block) {
			$text = $this->markup->block($text);

		} else {
			$text = $this->markup->line($text);
		}

		// Resolve @link and @see
		$text = $this->resolveLinks($text, $context);

		return $text;
	}


	/**
	 * Resolves links in documentation.
	 *
	 * @param string $text Processed documentation text
	 * @param ReflectionElement $context Reflection object
	 * @return string
	 */
	public function resolveLinks($text, ReflectionElement $context)
	{
		$that = $this;
		return preg_replace_callback('~{@(?:link|see)\\s+([^}]+)}~', function ($matches) use ($context, $that) {
			// Texy already added <a> so it has to be stripped
			list($url, $description) = Strings::split(strip_tags($matches[1]));
			if (Validators::isUri($url)) {
				return Strings::link($url, $description ?: $url);
			}
			return $that->resolveLink($matches[1], $context) ?: $matches[1];
		}, $text);
	}


	/**
	 * Resolves internal annotation.
	 *
	 * @param string $text
	 * @return string
	 */
	private function resolveInternal($text)
	{
		$options = $this->configuration->getOptions();
		$internal = $options['internal'];
		$annotationsMask = '~\\{@(\\w+)(?:(?:\\s+((?>(?R)|[^{}]+)*)\\})|\\})~';
		return preg_replace_callback($annotationsMask, function ($matches) use ($internal) {
			// Replace only internal
			if ($matches[1] !== 'internal') {
				return $matches[0];
			}
			return $internal && isset($matches[2]) ? $matches[2] : '';
		}, $text);
	}


	/********************* highlight *********************/

	/**
	 * @param string $source
	 * @param mixed $context
	 * @return mixed
	 */
	public function highlightPhp($source, $context)
	{
		return $this->resolveLink($this->getTypeName($source), $context) ?: $this->highlighter->highlight((string) $source);
	}


	/**
	 * @param string $definition
	 * @param mixed $context
	 * @return mixed
	 */
	public function highlightValue($definition, $context)
	{
		return $this->highlightPhp(preg_replace('~^(?:[ ]{4}|\t)~m', '', $definition), $context);
	}

}
