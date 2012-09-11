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

use ApiGen\Reflection\ReflectionClass;
use ApiGen\Reflection\ReflectionConstant;
use ApiGen\Reflection\ReflectionElement;
use ApiGen\Reflection\ReflectionFunction;
use ApiGen\Reflection\ReflectionMethod;
use ApiGen\Reflection\ReflectionParameter;
use ApiGen\Reflection\ReflectionProperty;

class Report
{
	private $elements;

	private $checks = array();

	/**
	 * @todo Use storage instead of array
	 */
	public function __construct(array $elements)
	{
		$this->elements = $elements;
	}

	public function addCheck(ICheck $check)
	{
		$this->checks[] = $check;

		return $this;
	}

	/**
	 * @throws \RuntimeException If file isn't writable.
	 */
	public function make($outputFile)
	{
		$messages = array();

		foreach ($this->elements as $parentElement) {
			// $fileName = $this->unPharPath($parentElement->getFileName());
			$fileName = $parentElement->getFileName();

			if (!$parentElement->isValid()) {
				$messages[$fileName][] = new Message(sprintf('Duplicate %s', static::getElementLabel($parentElement)), 0);
				continue;
			}

			// Skip elements not from the main project
			if (!$parentElement->isMain()) {
				continue;
			}

			// Internal elements don't have documentation
			if ($parentElement->isInternal()) {
				continue;
			}

			$elements = array($parentElement);
			if ($parentElement instanceof ReflectionClass) {
				$elements = array_merge(
					$elements,
					array_values($parentElement->getOwnMethods()),
					array_values($parentElement->getOwnConstants()),
					array_values($parentElement->getOwnProperties())
				);
			}

			foreach ($elements as $element) {
				foreach ($this->checks as $check) {
					foreach ($check->check($element) as $message) {
						$messages[$fileName][] = $message;
					}
				}
			}
		}
		uksort($messages, 'strcasecmp');

		$file = @fopen($outputFile, 'w');
		if (false === $file) {
			throw new \RuntimeException(sprintf('File "%s" isn\'t writable', $outputFile));
		}
		fwrite($file, sprintf('<?xml version="1.0" encoding="UTF-8"?>%s', "\n"));
		fwrite($file, sprintf('<checkstyle version="1.3.0">%s', "\n"));
		foreach ($messages as $fileName => $fileMessages) {
			fwrite($file, sprintf('%s<file name="%s">%s', "\t", $fileName, "\n"));

			// Sort by line
			usort($fileMessages, function(Message $one, Message $two) {
				return strnatcmp($one->getLine(), $two->getLine());
			});

			foreach ($fileMessages as $message) {
				$text = preg_replace('~\\s+~u', ' ', $message->getText());
				fwrite($file, sprintf('%s<error severity="%s" line="%s" message="%s" source="ApiGen.Documentation.Documentation"/>%s', "\t\t", $message->getSeverity(), $message->getLine(), htmlspecialchars($text), "\n"));
			}

			fwrite($file, sprintf('%s</file>%s', "\t", "\n"));
		}
		fwrite($file, sprintf('</checkstyle>%s', "\n"));
		fclose($file);

		return $this;
	}

	/**
	 * @todo Move to a helper class?
	 * @param \ApiGen\Reflection\ReflectionElement|ApiGen\Reflection\ReflectionParameter
	 */
	public static function getElementLabel($element)
	{
		if ($element instanceof ReflectionClass) {
			if ($element->isInterface()) {
				$label = 'interface';
			} elseif ($element->isTrait()) {
				$label = 'trait';
			} elseif ($element->isException()) {
				$label = 'exception';
			} else {
				$label = 'class';
			}
		} elseif ($element instanceof ReflectionMethod) {
			$label = 'method';
		} elseif ($element instanceof ReflectionFunction) {
			$label = 'function';
		} elseif ($element instanceof ReflectionConstant) {
			$label = 'constant';
		} elseif ($element instanceof ReflectionProperty) {
			$label = 'property';
		} elseif ($element instanceof ReflectionParameter) {
			$label = 'parameter';
		}
		return sprintf('%s %s', $label, $element->getPrettyName());
	}
}
