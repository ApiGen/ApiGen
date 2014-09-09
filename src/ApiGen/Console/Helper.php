<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Console;

use Nette;


class Helper extends Nette\Object
{

	/**
	 * @return array
	 * @throws Nette\UnexpectedValueException
	 */
	public function getCliArguments()
	{
		$argv = array_slice($_SERVER['argv'], 1);

		$params = array();

		$current = NULL;
		foreach ($argv as $argument) {
			if (preg_match('~^--([a-z][-a-z]*[a-z])(?:=(.+))?$~', $argument, $matches) || preg_match('~^-([a-z])=?(.*)~', $argument, $matches)) {
				if (isset($matches[2])) {
					$current = NULL;
					$params[$matches[1]][] = $matches[2];

				} else {
					$current = $matches[1];
					$params[$current][] = TRUE;
				}

			} elseif ($current !== NULL) {
				array_pop($params[$current]);
				$params[$current][] = $argument;
				$current = NULL;

			} else {
				throw new Nette\UnexpectedValueException(sprintf('Invalid option "%s" found.', $argument));
			}
		}

		return $params;
	}

}
