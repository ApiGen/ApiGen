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
use ApiGen\Generator\Markups\Markup;
use ApiGen\Generator\Resolvers\ElementResolver;
use ApiGen\Generator\SourceCodeHighlighter\SourceCodeHighlighter;
use ApiGen\Reflection\ReflectionClass;
use ApiGen\Reflection\ReflectionConstant;
use ApiGen\Reflection\ReflectionElement;
use ApiGen\Reflection\ReflectionFunction;
use ApiGen\Reflection\ReflectionMethod;
use ApiGen\Reflection\ReflectionProperty;
use ApiGen\Templating\Filters\Helpers\LinkBuilder;
use ApiGen\Templating\Filters\Helpers\Strings;
use Latte\Runtime\Filters as LatteFilters;
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

	/**
	 * @var LinkBuilder
	 */
	private $linkBuilder;


	public function __construct(
		Configuration $configuration,
		SourceCodeHighlighter $highlighter,
		Markup $markup,
		ElementResolver $elementResolver,
		LinkBuilder $linkBuilder
	) {
		$this->highlighter = $highlighter;
		$this->markup = $markup;
		$this->elementResolver = $elementResolver;
		$this->configuration = $configuration;
		$this->linkBuilder = $linkBuilder;
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
	 * @param string|ReflectionClass $class
	 * @return string
	 */
	public function classUrl($class)
	{
		$className = $class instanceof ReflectionClass ? $class->getName() : $class;
		return sprintf(
			$this->configuration->getOptions()[CO::TEMPLATE]['templates']['class']['filename'],
			$this->urlize($className)
		);
	}


	/**
	 * @return string
	 */
	public function methodUrl(ReflectionMethod $method, ReflectionClass $class = NULL)
	{
		$className = $class !== NULL ? $class->getName() : $method->getDeclaringClassName();
		return $this->classUrl($className) . '#' . ($method->isMagic() ? 'm' : '') . '_'
			. ($method->getOriginalName() ?: $method->getName());
	}


	/**
	 * @return string
	 */
	public function propertyUrl(ReflectionProperty $property, ReflectionClass $class = NULL)
	{
		$className = $class !== NULL ? $class->getName() : $property->getDeclaringClassName();
		return $this->classUrl($className) . '#' . ($property->isMagic() ? 'm' : '') . '$' . $property->getName();
	}


	/**
	 * @return string
	 */
	public function constantUrl(ReflectionConstant $constant)
	{
		// Class constant
		if ($className = $constant->getDeclaringClassName()) {
			return $this->classUrl($className) . '#' . $constant->getName();
		}
		// Constant in namespace or global space
		return sprintf(
			$this->configuration->getOptions()[CO::TEMPLATE]['templates']['constant']['filename'],
			$this->urlize($constant->getName())
		);
	}


	/**
	 * @return string
	 */
	public function functionUrl(ReflectionFunction $function)
	{
		return sprintf(
			$this->configuration->getOptions()[CO::TEMPLATE]['templates']['function']['filename'],
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

		$classes = [];
		if ($element->isDeprecated()) {
			$classes[] = 'deprecated';
		}

		/** @var ReflectionClass|ReflectionConstant $element */
		if ( ! $element->isValid()) {
			$classes[] = 'invalid';
		}

		$link = $this->createLinkForElement($element, $classes);
		return sprintf('<code>%s</code>', $link . $suffix);
	}


	/**
	 * @param string $value
	 * @param string $name
	 * @param ReflectionElement $context
	 * @return string
	 */
	public function annotation($value, $name, ReflectionElement $context)
	{
		$annotationProcessors = [
			'return' => $this->processReturnAnnotations($value, $context),
			'throws' => $this->processThrowsAnnotations($value, $context),
			'license' => $this->processLicenseAnnotations($value),
			'link' => $this->processLinkAnnotations($value),
			'see' => $this->processSeeAnnotations($value, $context),
			'uses' => $this->processUsesAndUsedbyAnnotations($value, $context),
			'usedby' => $this->processUsesAndUsedbyAnnotations($value, $context),
		];

		if (isset($annotationProcessors[$name])) {
			return $annotationProcessors[$name];
		}

		return $this->doc($value, $context);
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
		$links = [];

		list($types) = Strings::split($annotation);
		if ( ! empty($types) && $types{0} === '$') {
			$types = NULL;
		}

		foreach (explode('|', $types) as $type) {
			$type = $this->getTypeName($type, FALSE);
			$links[] = $this->resolveLink($type, $context) ?: LatteFilters::escapeHtml(ltrim($type, '\\'));
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
	 * @return string
	 */
	public function shortDescription($element, $block = FALSE)
	{
		return $this->doc($element->getShortDescription(), $element, $block);
	}


	/**
	 * @param ReflectionElement $element
	 * @return string
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
	 * @param bool $block Parse text as block
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
		return preg_replace_callback('~{@(?:link|see)\\s+([^}]+)}~', function ($matches) use ($context) {
			// Texy already added <a> so it has to be stripped
			list($url, $description) = Strings::split(strip_tags($matches[1]));
			if (Validators::isUri($url)) {
				return $this->linkBuilder->build($url, $description ?: $url);
			}
			return $this->resolveLink($matches[1], $context) ?: $matches[1];
		}, $text);
	}


	/**
	 * @param string $text
	 * @return string
	 */
	private function resolveInternal($text)
	{
		$pattern = '~\\{@(\\w+)(?:(?:\\s+((?>(?R)|[^{}]+)*)\\})|\\})~';
		return preg_replace_callback($pattern, function ($matches) {
			// Replace only internal
			if ($matches[1] !== 'internal') {
				return $matches[0];
			}
			return $this->configuration->getOptions()[CO::INTERNAL] && isset($matches[2]) ? $matches[2] : '';
		}, $text);
	}


	/********************* highlight *********************/

	/**
	 * @param string $source
	 * @param mixed $context
	 * @return string
	 */
	public function highlightPhp($source, $context)
	{
		return $this->resolveLink($this->getTypeName($source), $context) ?: $this->highlighter->highlight((string) $source);
	}


	/**
	 * @param string $definition
	 * @param mixed $context
	 * @return string
	 */
	public function highlightValue($definition, $context)
	{
		return $this->highlightPhp(preg_replace('~^(?:[ ]{4}|\t)~m', '', $definition), $context);
	}


	/**
	 * @param ReflectionElement $element
	 * @param array $classes
	 * @return string
	 */
	private function createLinkForElement($element, array $classes)
	{
		if ($element instanceof ReflectionClass) {
			return $this->linkBuilder->build($this->classUrl($element), $element->getName(), TRUE, $classes);

		} elseif ($element instanceof ReflectionConstant && $element->getDeclaringClassName() === NULL) {
			return $this->createLinkForGlobalConstant($element, $classes);

		} elseif ($element instanceof ReflectionFunction) {
			return $this->linkBuilder->build($this->functionUrl($element), $element->getName() . '()', TRUE, $classes);

		} else {
			return $this->createLinkForPropertyMethodOrConstants($element, $classes);
		}
	}


	/**
	 * @return string
	 */
	private function createLinkForGlobalConstant(ReflectionConstant $element, array $classes)
	{
		$text = $element->inNamespace()
			? LatteFilters::escapeHtml($element->getNamespaceName()) . '\\<b>'
			. LatteFilters::escapeHtml($element->getShortName()) . '</b>'
			: '<b>' . LatteFilters::escapeHtml($element->getName()) . '</b>';

		return $this->linkBuilder->build($this->constantUrl($element), $text, FALSE, $classes);
	}


	/**
	 * @param ReflectionMethod|ReflectionProperty|ReflectionConstant $element
	 * @param array $classes
	 * @return string
	 */
	private function createLinkForPropertyMethodOrConstants($element, array $classes)
	{
		$url = '';
		$text = LatteFilters::escapeHtml($element->getDeclaringClassName());
		if ($element instanceof ReflectionProperty) {
			$url = $this->propertyUrl($element);
			$text .= '::<var>$' . LatteFilters::escapeHtml($element->getName()) . '</var>';

		} elseif ($element instanceof ReflectionMethod) {
			$url = $this->methodUrl($element);
			$text .= '::' . LatteFilters::escapeHtml($element->getName()) . '()';

		} elseif ($element instanceof ReflectionConstant) {
			$url = $this->constantUrl($element);
			$text .= '::<b>' . LatteFilters::escapeHtml($element->getName()) . '</b>';
		}

		return $this->linkBuilder->build($url, $text, FALSE, $classes);
	}


	/**
	 * @param string $value
	 * @param ReflectionElement $context
	 * @return string
	 */
	private function processReturnAnnotations($value, ReflectionElement $context)
	{
		$description = $this->getDescriptionFromValue($value, $context);
		$typeLinks = $this->typeLinks($value, $context);
		return sprintf('<code>%s</code>%s', $typeLinks, $description);
	}


	/**
	 * @param string $value
	 * @param ReflectionElement $context
	 * @return string
	 */
	private function processThrowsAnnotations($value, ReflectionElement $context)
	{
		$description = $this->getDescriptionFromValue($value, $context);
		$typeLinks = $this->typeLinks($value, $context);
		return $typeLinks . $description;
	}


	/**
	 * @param mixed $value
	 * @param ReflectionElement $context
	 * @return string
	 */
	private function getDescriptionFromValue($value, ReflectionElement $context)
	{
		$description = trim(strpbrk($value, "\n\r\t $")) ?: NULL;
		if ($description) {
			$description = '<br>' . $this->doc($description, $context);
		}
		return $description;
	}


	/**
	 * @param string $value
	 * @return string
	 */
	private function processLicenseAnnotations($value)
	{
		list($url, $description) = Strings::split($value);
		return $this->linkBuilder->build($url, $description ?: $url);
	}


	/**
	 * @param string $value
	 * @return string
	 */
	private function processLinkAnnotations($value)
	{
		list($url, $description) = Strings::split($value);
		if (Validators::isUri($url)) {
			return $this->linkBuilder->build($url, $description ?: $url);
		}
		return NULL;
	}


	/**
	 * @param string $value
	 * @param ReflectionElement $context
	 * @return string
	 */
	private function processSeeAnnotations($value, ReflectionElement $context)
	{
		$doc = [];
		foreach (preg_split('~\\s*,\\s*~', $value) as $link) {
			if ($this->elementResolver->resolveElement($link, $context) !== NULL) {
				$doc[] = sprintf('<code>%s</code>', $this->typeLinks($link, $context));

			} else {
				$doc[] = $this->doc($link, $context);
			}
		}
		return implode(', ', $doc);
	}


	/**
	 * @param string $value
	 * @param ReflectionElement $context
	 * @return string
	 */
	private function processUsesAndUsedbyAnnotations($value, ReflectionElement $context)
	{
		list($link, $description) = Strings::split($value);
		$separator = $context instanceof ReflectionClass || ! $description ? ' ' : '<br>';
		if ($this->elementResolver->resolveElement($link, $context) !== NULL) {
			return sprintf('<code>%s</code>%s%s', $this->typeLinks($link, $context), $separator, $description);
		}
		return NULL;
	}

}
