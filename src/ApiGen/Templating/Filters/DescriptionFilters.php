<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Templating\Filters;

use ApiGen\Reflection\ReflectionElement;
use ApiGen\Templating\Filters\Helpers\TextFormatter;
use Nette;


class DescriptionFilters extends Filters
{

	/**
	 * @var TextFormatter
	 */
	private $textFormatter;


	public function __construct(TextFormatter $textFormatter)
	{
		$this->textFormatter = $textFormatter;
	}


	/**
	 * Docblock description
	 */
	public function description($annotation, $context)
	{
		$description = trim(strpbrk($annotation, "\n\r\t $")) ?: $annotation;
		return $this->textFormatter->doc($description, $context);
	}


	/**
	 * @param ReflectionElement $element
	 * @param bool $block
	 * @return mixed
	 */
	public function shortDescription($element, $block = FALSE)
	{
		return $this->textFormatter->doc($element->getShortDescription(), $element, $block);
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

		return $this->textFormatter->doc($long, $element, TRUE);
	}

}
