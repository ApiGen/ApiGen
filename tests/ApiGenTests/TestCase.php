<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGenTests;

use Tester;
use Tester\Environment;


/**
 * Basic test case that prepares and removed apigen.neon,
 * which is required for some commands.
 */
class TestCase extends Tester\TestCase
{

	protected function setUp()
	{
		Environment::lock('config', dirname(TEMP_DIR));
		file_put_contents('apigen.neon', '');
	}


	protected function tearDown()
	{
		unlink('apigen.neon');
	}


	/**
	 * @param string $file
	 * @return string
	 */
	protected function getFileContentInOneLine($file)
	{
		$content = file_get_contents($file);
		$content = preg_replace('/\s+/', ' ', $content);
		$content = preg_replace('/(?<=>)\s+|\s+(?=<)/', '', $content);
		return $content;
	}


	/**
	 * @param string $options
	 * @param string $source
	 * @param string $bin
	 * @return mixed
	 */
	protected function runGenerateCommand($options = NULL, $source = NULL, $bin = NULL)
	{
		$cliInput = $bin ?: APIGEN_BIN . ' generate -s ' . $source ?: PROJECT_DIR . ' -d ' . API_DIR;
		$cliInput .= ($options ? ' ' . $options : NULL);

		passthru($cliInput, $output);
		return $output;
	}

}
