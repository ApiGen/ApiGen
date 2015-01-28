<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Console;

use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;


class IO
{

	/**
	 * @var InputInterface
	 */
	private $input;

	/**
	 * @var OutputInterface
	 */
	private $output;


	public function __construct()
	{
		$this->input = new ArrayInput([]);
		$this->output = new NullOutput;
	}


	/**
	 * @return InputInterface
	 */
	public function getInput()
	{
		return $this->input;
	}


	public function setInput(InputInterface $input)
	{
		$this->input = $input;
	}


	/**
	 * @return OutputInterface
	 */
	public function getOutput()
	{
		return $this->output;
	}


	public function setOutput(OutputInterface $output)
	{
		$this->output = $output;
	}

}
