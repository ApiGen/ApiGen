<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Git;

use Nette;
use RuntimeException;


/**
 * @method VersionSwitcher setSource()
 */
class VersionSwitcher extends Nette\Object
{

	/**
	 * @var string
	 */
	private $source;

	/**
	 * @var string
	 */
	private $initBranch;


	/**
	 * @param string $version
	 */
	public function switchToVersion($version)
	{
		$this->saveInitBranch();
		$this->checkIfRepoIsClean();
		passthru('git checkout -q ' . $version);
	}


	public function restoreInitBranch()
	{
		passthru('git checkout -q ' . $this->initBranch);
	}


	private function saveInitBranch()
	{
		if ($this->initBranch === NULL) {
			exec('git symbolic-ref --short -q HEAD', $output);
			if (is_string($output[0])) {
				$this->initBranch = $output[0];
			}
		}
	}


	private function checkIfRepoIsClean()
	{
		exec('git status --porcelain | grep -v ' . $this->source . ' | wc -l', $output);
		if ($output[0] > 0) {
			throw new RuntimeException('Unable to switch version as the repository is not clean.');
		}
	}

}
