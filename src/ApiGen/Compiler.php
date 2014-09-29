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
use Nette\Utils\Strings;
use Phar;
use SplFileInfo;
use Symfony\Component\Process\Process;


/**
 * Compiles apigen into a phar.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Jordi Boggiano <j.boggiano@seld.be>
 * @author Tomas Votruba <tomas.vot@gmail.com>
 */
class Compiler extends Nette\Object
{

	/**
	 * @var string
	 */
	private $version;

	/**
	 * @var string
	 */
	private $versionDate;


	/**
	 * @throws \RuntimeException
	 * @param string $pharFile
	 */
	public function compile($pharFile = 'apigen.phar')
	{
		if (file_exists($pharFile)) {
			unlink($pharFile);
		}

		$process = new Process('git log --pretty="%H" -n1 HEAD', __DIR__);
		if ($process->run() != 0) {
			throw new \RuntimeException('Can\'t run git log. You must ensure to run compile from Apigen git repository clone and that git binary is available.');
		}
		$this->version = trim($process->getOutput());

		$process = new Process('git log -n1 --pretty=%ci HEAD', __DIR__);
		if ($process->run() != 0) {
			throw new \RuntimeException('Can\'t run git log. You must ensure to run compile from Apigen git repository clone and that git binary is available.');
		}
		$date = new \DateTime(trim($process->getOutput()));
		$date->setTimezone(new \DateTimeZone('UTC'));
		$this->versionDate = $date->format('Y-m-d H:i:s');

		$process = new Process('git describe --tags HEAD');
		if ($process->run() == 0) {
			$this->version = trim($process->getOutput());
		}

		$phar = new Phar($pharFile, 0, 'apigen.phar');
		$phar->setSignatureAlgorithm(Phar::SHA1);

		$phar->startBuffering();

		foreach (Finder::findFiles('*.php', '*.neon')->from(__DIR__ . '/../../src') as $file) {
			$this->addFile($phar, $file);
		}

		foreach (Finder::findFiles('*.php')->from(__DIR__ . '/../../vendor') as $file) {
			$this->addFile($phar, $file);
		}

		$this->addBin($phar);

		$phar->setStub($this->getStub());

		$phar->stopBuffering();

		$this->addFile($phar, new SplFileInfo(__DIR__ . '/../../license.md'), FALSE);

		unset($phar);
	}


	/**
	 * @param Phar $phar
	 * @param SplFileInfo $file
	 * @param bool $strip
	 */
	private function addFile(Phar $phar, SplFileInfo $file, $strip = TRUE)
	{
		$path = strtr(str_replace(dirname(dirname(__DIR__)) . DS, '', $file->getRealPath()), '\\', '/');

		$content = file_get_contents($file);
		if ($strip) {
			$content = $this->stripWhitespace($content);

		} elseif (basename($file) === 'license') {
			$content = PHP_EOL . $content . PHP_EOL;
		}

		if ($path === 'src/ApiGen/ApiGen.php') {
			$content = str_replace('@package_version@', $this->version, $content);
			$content = str_replace('@release_date@', $this->versionDate, $content);
		}

		$phar->addFromString($path, $content);
	}


	private function addBin(Phar $phar)
	{
		$content = file_get_contents(__DIR__ . '/../../bin/apigen');
		$content = preg_replace('{^#!/usr/bin/env php\s*}', '', $content);
		$phar->addFromString('bin/apigen', $content);
	}


	/**
	 * Removes whitespace from a PHP source string while preserving line numbers.
	 * Keeps docblocks with @method annotations.
	 *
	 * @param  string $source A PHP string
	 * @return string The PHP string with the whitespace removed
	 */
	private function stripWhitespace($source)
	{
		if ( ! function_exists('token_get_all')) {
			return $source;
		}

		$output = '';
		foreach (token_get_all($source) as $token) {
			if (is_string($token)) {
				$output .= $token;

			} elseif (in_array($token[0], array(T_COMMENT, T_DOC_COMMENT)) && ! Strings::contains($token[1], '@method')) {
				$output .= str_repeat(PHP_EOL, substr_count($token[1], PHP_EOL));

			} elseif ($token[0] === T_WHITESPACE) {
				// reduce wide spaces
				$whitespace = preg_replace('{[ \t]+}', ' ', $token[1]);
				// normalize newlines to \n
				$whitespace = preg_replace('{(?:\r\n|\r|\n)}', "\n", $whitespace);
				// trim leading spaces
				$whitespace = preg_replace('{\n +}', "\n", $whitespace);
				$output .= $whitespace;

			} else {
				$output .= $token[1];
			}
		}

		return $output;
	}


	/**
	 * @return string
	 */
	private function getStub()
	{
		$stub = <<<'EOF'
#!/usr/bin/env php
<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

Phar::mapPhar('apigen.phar');

EOF;

		return $stub . <<<'EOF'
require 'phar://apigen.phar/bin/apigen';

__HALT_COMPILER();
EOF;
	}

}
