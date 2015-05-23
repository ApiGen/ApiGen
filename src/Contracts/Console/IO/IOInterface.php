<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Contracts\Console\IO;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


interface IOInterface
{

	/**
	 * @return InputInterface
	 */
	function getInput();


	/**
	 * @return OutputInterface
	 */
	function getOutput();


	/**
	 * @param string $message
	 */
	function writeln($message);


	/**
	 * @param string $question
	 * @param NULL|string $default
	 * @return string
	 */
	function ask($question, $default = NULL);

}
