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
use ApiGen\Templating\Filters\Helpers\ElementUrlFactory;
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

	/**
	 * @var ElementUrlFactory
	 */
	private $elementUrlFactory;


	public function __construct(
		Configuration $configuration,
		SourceCodeHighlighter $highlighter,
		Markup $markup,
		ElementResolver $elementResolver,
		LinkBuilder $linkBuilder,
		ElementUrlFactory $elementUrlFactory
	) {
		$this->highlighter = $highlighter;
		$this->markup = $markup;
		$this->elementResolver = $elementResolver;
		$this->configuration = $configuration;
		$this->linkBuilder = $linkBuilder;
		$this->elementUrlFactory = $elementUrlFactory;
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
	 * @param string $annotation
	 * @param ReflectionElement $reflectionElement
	 * @return string
	 */
	public function description($annotation, ReflectionElement $reflectionElement)
	{
		$description = trim(strpbrk($annotation, "\n\r\t $")) ?: $annotation;
		return $this->doc($description, $reflectionElement);
	}


	/**
	 * @param ReflectionElement $reflectionElement
	 * @param bool $block
	 * @return string
	 */
	public function shortDescription(ReflectionElement $reflectionElement, $block = FALSE)
	{
		return $this->doc($reflectionElement->getShortDescription(), $reflectionElement, $block);
	}


	/**
	 * @return string
	 */
	public function longDescription(ReflectionElement $element)
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
	 * @param string $text
	 * @param ReflectionElement $reflectionElement
	 * @param bool $block
	 * @return string
	 */
	public function doc($text, ReflectionElement $reflectionElement, $block = FALSE)
	{
		$text = $this->resolveInternalAnnotation($text);

		// Process markup
		if ($block) {
			$text = $this->markup->block($text);

		} else {
			$text = $this->markup->line($text);
		}

		return $this->resolveLinkAndSeeAnnotation($text, $reflectionElement);
	}


	/**
	 * @param string $text
	 * @return string
	 */
	private function resolveInternalAnnotation($text)
	{
		$pattern = '~\\{@(\\w+)(?:(?:\\s+((?>(?R)|[^{}]+)*)\\})|\\})~';
		return preg_replace_callback($pattern, function ($matches) {
			if ($matches[1] !== 'internal') {
				return $matches[0];
			}

			if ($this->configuration->getOption(CO::INTERNAL) && isset($matches[2])) {
				return $matches[2];
			}

			return '';
		}, $text);
	}


	/**
	 * @param string $text
	 * @param ReflectionElement $reflectionElement
	 * @return string
	 */
	private function resolveLinkAndSeeAnnotation($text, ReflectionElement $reflectionElement)
	{
		return preg_replace_callback('~{@(?:link|see)\\s+([^}]+)}~', function ($matches) use ($reflectionElement) {
			list($url, $description) = Strings::split($matches[1]);

			if (Validators::isUri($url)) {
				return $this->linkBuilder->build($url, $description ?: $url);
			}

			if ($link = $this->resolveLink($matches[1], $reflectionElement)) {
				return $link;
			}

			return $matches[1];
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
			return $this->linkBuilder->build(
				$this->elementUrlFactory->createForClass($element), $element->getName(), TRUE, $classes
			);

		} elseif ($element instanceof ReflectionConstant && $element->getDeclaringClassName() === NULL) {
			return $this->createLinkForGlobalConstant($element, $classes);

		} elseif ($element instanceof ReflectionFunction) {
			return $this->linkBuilder->build(
				$this->elementUrlFactory->createForFunction($element), $element->getName() . '()', TRUE, $classes
			);

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

		return $this->linkBuilder->build($this->elementUrlFactory->createForConstant($element), $text, FALSE, $classes);
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
			$url = $this->elementUrlFactory->createForProperty($element);
			$text .= '::<var>$' . LatteFilters::escapeHtml($element->getName()) . '</var>';

		} elseif ($element instanceof ReflectionMethod) {
			$url = $this->elementUrlFactory->createForMethod($element);
			$text .= '::' . LatteFilters::escapeHtml($element->getName()) . '()';

		} elseif ($element instanceof ReflectionConstant) {
			$url = $this->elementUrlFactory->createForConstant($element);
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
