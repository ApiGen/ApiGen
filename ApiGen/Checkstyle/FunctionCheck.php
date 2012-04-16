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
use ApiGen\Reflection\ReflectionFunction;
use ApiGen\Reflection\ReflectionMethod;

class FunctionCheck implements ICheck
{
	public function check(ReflectionElement $element)
	{
		if (!$element instanceof ReflectionMethod && !$element instanceof ReflectionFunction) {
			return array();
		}

		$messages = array();

		$annotations = $element->getAnnotations();
		$label = Report::getElementLabel($element);
		$line = $element->getStartLine();

		// Parameters
		foreach ($element->getParameters() as $no => $parameter) {
			if (!isset($annotations['param'][$no])) {
				$messages[] = new Message(sprintf('Missing documentation of %s', Report::getElementLabel($parameter)), $line);
				continue;
			}

			if (!preg_match('~^[\\w\\\\]+(?:\\[\\])?(?:\\|[\\w\\\\]+(?:\\[\\])?)*(?:\\s+\\$' . $parameter->getName() . ')?(?:\\s+.+)?$~s', $annotations['param'][$no])) {
				$messages[] = new Message(sprintf('Invalid documentation "%s" of %s', $annotations['param'][$no], Report::getElementLabel($parameter)), $line, Message::SEVERITY_WARNING);
			}

			unset($annotations['param'][$no]);
		}
		if (isset($annotations['param'])) {
			foreach ($annotations['param'] as $annotation) {
				$messages[] = new Message(sprintf('Existing documentation "%s" of nonexistent parameter of %s', $annotation, $label), $line, Message::SEVERITY_WARNING);
			}
		}

		$parentElement = $element instanceof ReflectionMethod ? $element->getDeclaringClass() : $element;
		$tokens = $parentElement->getBroker()->getFileTokens($parentElement->getFileName());

		// Return values
		$return = false;
		$tokens->seek($element->getStartPosition())
			->find(T_FUNCTION);
		while ($tokens->next() && $tokens->key() < $element->getEndPosition()) {
			$type = $tokens->getType();
			if (T_FUNCTION === $type) {
				// Skip annonymous functions
				$tokens->find('{')->findMatchingBracket();
			} elseif (T_RETURN === $type && !$tokens->skipWhitespaces()->is(';')) {
				// Skip return without return value
				$return = true;
				break;
			}
		}
		if ($return && !isset($annotations['return'])) {
			$messages[] = new Message(sprintf('Missing documentation of return value of %s', $label), $line);
		} elseif (isset($annotations['return'])) {
			if (!$return && 'void' !== $annotations['return'][0] && ($element instanceof Reflection\ReflectionFunction || (!$parentElement->isInterface() && !$element->isAbstract()))) {
				$messages[] = new Message(sprintf('Existing documentation "%s" of nonexistent return value of %s', $annotations['return'][0], $label), $line, Message::SEVERITY_WARNING);
			} elseif (!preg_match('~^[\\w\\\\]+(?:\\[\\])?(?:\\|[\\w\\\\]+(?:\\[\\])?)*(?:\\s+.+)?$~s', $annotations['return'][0])) {
				$messages[] = new Message(sprintf('Invalid documentation "%s" of return value of %s', $annotations['return'][0], $label), $line, Message::SEVERITY_WARNING);
			}
		}
		if (isset($annotations['return'][1])) {
			$messages[] = new Message(sprintf('Duplicate documentation "%s" of return value of %s', $annotations['return'][1], $label), $line, Message::SEVERITY_WARNING);
		}

		// Throwing exceptions
		$throw = false;
		$tokens->seek($element->getStartPosition())
			->find(T_FUNCTION);
		while ($tokens->next() && $tokens->key() < $element->getEndPosition()) {
			$type = $tokens->getType();
			if (T_TRY === $type) {
				// Skip try
				$tokens->find('{')->findMatchingBracket();
			} elseif (T_THROW === $type) {
				$throw = true;
				break;
			}
		}
		if ($throw && !isset($annotations['throws'])) {
			$messages[] = new Message(sprintf('Missing documentation of throwing an exception of %s', $label), $line);
		} elseif (isset($annotations['throws'])	&& !preg_match('~^[\\w\\\\]+(?:\\|[\\w\\\\]+)*(?:\\s+.+)?$~s', $annotations['throws'][0])) {
			$messages[] = new Message(sprintf('Invalid documentation "%s" of throwing an exception of %s', $annotations['throws'][0], $label), $line, Message::SEVERITY_WARNING);
		}

		return $messages;
	}
}
