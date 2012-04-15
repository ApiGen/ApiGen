<?php

/**
 * ApiGen 3.0dev - API documentation generator for PHP 5.3+
 *
 * Copyright (c) 2010-2011 David Grudl (http://davidgrudl.com)
 * Copyright (c) 2011-2012 Jaroslav Hanslík (https://github.com/kukulich)
 * Copyright (c) 2011-2012 Ondřej Nešpor (https://github.com/Andrewsville)
 *
 * For the full copyright and license information, please view
 * the file LICENSE.md that was distributed with this source code.
 */

namespace ApiGen\Checkstyle;

use ApiGen\Reflection\ReflectionElement;

class DataTypeCheck implements ICheck
{
	public function register()
	{
		return array(
			'ApiGen\\Reflection\\ReflectionProperty',
			'ApiGen\\Reflection\\ReflectionConstant'
		);
	}

	public function check(ReflectionElement $element)
	{
		$messages = array();

		$annotations = $element->getAnnotations();

		if (!isset($annotations['var'])) {
			$messages[] = new Message(sprintf('Missing documentation of the data type of %s', Report::getElementLabel($element)), $element->getStartLine());
		} elseif (!preg_match('~^[\\w\\\\]+(?:\\[\\])?(?:\\|[\\w\\\\]+(?:\\[\\])?)*(?:\\s+.+)?$~s', $annotations['var'][0])) {
			$messages[] = new Message(sprintf('Invalid documentation "%s" of the data type of %s', $annotations['var'][0], Report::getElementLabel($element)), $element->getStartLine(), Message::SEVERITY_WARNING);
		}

		if (isset($annotations['var'][1])) {
			$messages[] = new Message(sprintf('Duplicate documentation "%s" of the data type of %s', $annotations['var'][1], Report::getElementLabel($element)), $element->getStartLine(), Message::SEVERITY_WARNING);
		}

		return $messages;
	}
}
