<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen;

use Nette;
use Nette\Utils\Finder;
use Phar;
use SplFileInfo;


/**
 * Creates PHAR from ApiGen source.
 */
class PharCompiler extends Nette\Object
{

	/**
	 * @var string
	 */
	private $repoDir;

	/**
	 * @var string
	 */
	private $version;

	/**
	 * @var string
	 */
	private $releaseDate;


	/**
	 * @param string $repoDir  path to Apigen source dir
	 * @throws \LogicException
	 * @throws \RuntimeException
	 */
	public function __construct($repoDir)
	{
		if ( ! is_dir($repoDir)) {
			throw new \LogicException("Path '$repoDir' is not a directory.");

		} elseif ( ! is_file("$repoDir/src/ApiGen/ApiGen.php")) {
			throw new \LogicException("Directory '$repoDir' does not contain ApiGen source code.");
		}
		$this->repoDir = realpath($repoDir);

		if ($this->execute('git describe --tags HEAD', $repoDir, $output) === 0) {
			$this->version = trim($output);

		} elseif ($this->execute('git log --pretty="%H" -n1 HEAD', $repoDir, $output) === 0) {
			$this->version = trim($output);

		} else {
			throw new \RuntimeException('Cannot run git log to find ApiGen version. '
				. 'Ensure that compile runs from cloned ApiGen git repository and the git command is available.');
		}

		if ($this->execute('git log -n1 --format=%cD HEAD', $repoDir, $output) !== 0) {
			throw new \RuntimeException('Unable to run git log to find release date.');
		}
		$this->releaseDate = \DateTime::createFromFormat(\DateTime::RFC2822, trim($output))
			->setTimezone(new \DateTimeZone('UTC'))
			->format('Y-m-d H:i:s');
	}


	/**
	 * @param string $pharFile  output PHAR file name
	 * @param string $sourceDir  apigen source directory
	 * @throws \RuntimeException
	 */
	public function compile($pharFile)
	{
		if ( ! class_exists('Phar') || ini_get('phar.readonly')) {
			throw new \RuntimeException("Enable PHAR extension and set directive 'phar.readonly' to 'off'.");
		}

		if (file_exists($pharFile)) {
			unlink($pharFile);
		}

		$phar = new Phar($pharFile);

		$phar->setStub(
"#!/usr/bin/env php
<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

Phar::mapPhar('apigen.phar');
require 'phar://apigen.phar/src/apigen.php';
__HALT_COMPILER();
");

		$phar->startBuffering();

		foreach (Finder::findFiles('*')->from("$this->repoDir/src") as $file) {
			$this->addFile($phar, $file);
		}

		$exclude = [
			'jakub-onderka/php-parallel-lint',
			'nette/*/Tests',
			'nette/tester',
			'mikulas/code-sniffs',
			'squizlabs',
			'symfony/*/*/Tests',
			'zenify/coding-standard'
		];
		foreach (Finder::findFiles('*.php')->from("$this->repoDir/vendor")->exclude($exclude) as $file) {
			$this->addFile($phar, $file);
		}

		$phar['license.md'] = file_get_contents("$this->repoDir/license.md");

		$phar->stopBuffering();
		$phar->compressFiles(Phar::GZ);
		unset($phar);

		chmod($pharFile, 0755);
	}


	/**
	 * Executes shell command.
	 * @param string $command
	 * @param string $cwd
	 * @param string $output
	 * @return int
	 */
	private function execute($command, $cwd, & $output)
	{
		$oldCwd = getcwd();
		chdir($cwd);
		exec($command, $tmp, $exitCode);
		chdir($oldCwd);
		$output = implode("\n", $tmp);

		return $exitCode;
	}


	/**
	 * @param Phar $phar
	 * @param SplFileInfo $file
	 */
	private function addFile(Phar $phar, SplFileInfo $file)
	{
		$content = file_get_contents($file);
		if ($file->getExtension() === 'php') {
			$content = $this->stripWhitespaces($content);
		}

		$path = str_replace($this->repoDir . DIRECTORY_SEPARATOR, '', $file->getRealPath());
		$path = strtr($path, DIRECTORY_SEPARATOR, '/');

		if ($path === 'src/ApiGen/ApiGen.php') {
			$content = strtr($content, [
				'@package_version@' => $this->version,
				'@release_date@' => $this->releaseDate,
			]);
		}

		$phar[$path] = $content;
	}


	/**
	 * Minifies PHP source. Preserves line numbers and @method annotations.
	 *
	 * @param string $code
	 * @return string
	 */
	private function stripWhitespaces($code)
	{
		$output = '';
		foreach (token_get_all($code) as $token) {
			if (is_string($token)) {
				$output .= $token;

			} elseif ($token[0] === T_COMMENT) {
				$output .= str_repeat("\n", substr_count($token[1], "\n"));

			} elseif ($this->isCommentWithoutAnnotations($token, ['@return', '@method'])) {
				$output .= str_repeat("\n", substr_count($token[1], "\n"));

			} elseif ($token[0] === T_WHITESPACE) {
				if (strpos($token[1], "\n") === FALSE) {
					$output .= ' ';

				} else {
					$output .= str_repeat("\n", substr_count($token[1], "\n"));
				}

			} else {
				$output .= $token[1];
			}
		}

		return $output;
	}


	/**
	 * @return bool
	 */
	private function isCommentWithoutAnnotations(array $token, array $annotationList)
	{
		if ($token[0] !== T_DOC_COMMENT) {
			return FALSE;
		}
		foreach ($annotationList as $annotation) {
			if (strpos($token[1], $annotation) !== FALSE) {
				return FALSE;
			}
		}
		return TRUE;
	}

}
