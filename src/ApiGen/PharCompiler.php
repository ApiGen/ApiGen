<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen;

use LogicException;
use Nette\Utils\Finder;
use Phar;
use RuntimeException;
use SplFileInfo;


/**
 * Creates PHAR from ApiGen source.
 */
class PharCompiler
{

	/**
	 * @var string
	 */
	private $repoDir;


	/**
	 * @param string $repoDir  path to Apigen source dir
	 * @throws LogicException
	 * @throws RuntimeException
	 */
	public function __construct($repoDir)
	{
		if ( ! is_dir($repoDir)) {
			throw new LogicException("Path '$repoDir' is not a directory.");

		} elseif ( ! is_file("$repoDir/src/ApiGen/ApiGen.php")) {
			throw new LogicException("Directory '$repoDir' does not contain ApiGen source code.");
		}
		$this->repoDir = realpath($repoDir);
	}


	/**
	 * @param string $pharFile  output PHAR file name
	 * @throws RuntimeException
	 */
	public function compile($pharFile)
	{
		if ( ! class_exists('Phar') || ini_get('phar.readonly')) {
			throw new RuntimeException("Enable PHAR extension and set directive 'phar.readonly' to 'off'.");
		}

		if (file_exists($pharFile)) {
			unlink($pharFile);
		}

		$phar = new Phar($pharFile);
		$phar->setStub($this->getStub());
		$phar->startBuffering();

		foreach (Finder::findFiles('*')->from("$this->repoDir/src") as $file) {
			$this->addFile($phar, $file);
		}

		$exclude = [
			'jakub-onderka/*',
			'nette/tester/*',
			'pdepend/pdepend/*',
			'mockery/mockery/*',
			'phpmd/phpmd/*',
			'phpunit/*',
			'sebastian/*',
			'squizlabs/*',
			'symfony/dependency-injection',
			'Tests',
			'tests',
			'theseer/fdomdocument/*',
			'zenify/coding-standard/*'
		];
		foreach (Finder::findFiles(['*.php', '*.json'])->from($this->repoDir . '/vendor')->exclude($exclude) as $file) {
			$this->addFile($phar, $file);
		}

		$phar['license.md'] = file_get_contents($this->repoDir . '/license.md');

		$phar->stopBuffering();
		$phar->compressFiles(Phar::GZ);
		unset($phar);

		chmod($pharFile, 0755);
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
		$phar[$path] = $content;
	}


	/**
	 * Minimizes PHP source. Preserves line numbers, @return and @method annotations.
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


	/**
	 * @return string
	 */
	private function getStub()
	{
		$stub = <<<EOF
#!/usr/bin/env php
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
EOF;
		return $stub;
	}

}
