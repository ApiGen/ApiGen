<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Templating\Filters\Helpers;

use ApiGen\Configuration\Configuration;
use ApiGen\Generator\Markups\Markup;
use ApiGen\Reflection\ReflectionElement;
use Nette;
use Nette\Utils\Validators;


class TextFormatter extends Nette\Object
{

	/**
	 * @var Markup
	 */
	private $markup;

	/**
	 * @var Configuration
	 */
	private $configuration;


	public function __construct(Markup $markup, Configuration $configuration)
	{
		$this->markup = $markup;
		$this->configuration = $configuration;
	}


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
		$internal = $this->configuration->internal;
		return preg_replace_callback('~\\{@(\\w+)(?:(?:\\s+((?>(?R)|[^{}]+)*)\\})|\\})~', function ($matches) use ($internal) {
			// Replace only internal
			if ($matches[1] !== 'internal') {
				return $matches[0];
			}
			return $internal && isset($matches[2]) ? $matches[2] : '';
		}, $text);
	}

}
