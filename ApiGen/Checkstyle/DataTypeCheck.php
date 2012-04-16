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

use ApiGen\Reflection\ReflectionConstant;
use ApiGen\Reflection\ReflectionElement;
use ApiGen\Reflection\ReflectionProperty;

class DataTypeCheck implements ICheck
{
	public function check(ReflectionElement $element)
	{
		if (!$element instanceof ReflectionProperty && !$element instanceof ReflectionConstant) {
			return array();
		}

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
