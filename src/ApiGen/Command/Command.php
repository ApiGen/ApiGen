<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Command;

use InvalidArgumentException;
use Symfony;
use Symfony\Component\Console\Input\InputInterface;


abstract class Command extends Symfony\Component\Console\Command\Command
{

	/**
	 * @throws InvalidArgumentException
	 * @param InputInterface $input
	 * @param array $apigen
	 * @param string $key
	 * @return mixed
	 */
	protected function getValueFromArgumentOrConfig(InputInterface $input, array $apigen, $key)
	{
		if ($input->getArgument($key) === NULL && ! isset($apigen[$key])) {
			throw new InvalidArgumentException(ucfirst($key) . " is missing. Add it via apigen.neon or '$key' argument.");
		}

		return $input->getArgument($key) ?: $apigen[$key];
	}


	/**
	 * @throws InvalidArgumentException
	 * @param InputInterface $input
	 * @param array $apigen
	 * @param string $key
	 * @return mixed
	 */
	protected function getValueFromOptionOrConfig(InputInterface $input, array $apigen, $key)
	{
		if ($input->getOption($key) === NULL && ! isset($apigen[$key])) {
			throw new InvalidArgumentException(ucfirst($key) . " is missing. Add it via apigen.neon or '--$key' option.");
		}

		return ($input->getOption($key) !== NULL) ? $input->getOption($key) : $apigen[$key];
	}

}
