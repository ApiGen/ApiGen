<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\FileSystem;

use Nette;


/**
 * @method setConfig(array $config)
 */
class Finder extends Nette\Object
{

	/**
	 * @var array
	 */
	private $config;


	/**
	 * Returns list of all generated files.
	 *
	 * @return array
	 */
	public function findGeneratedFiles()
	{
		$files = array();

		// Resources
		foreach ($this->config['template']['resources'] as $item) {
			$path = dirname($this->config['templateConfig']) . '/' . $item;
			if (is_dir($path)) {
				$iterator = Nette\Utils\Finder::findFiles('*')->from($path)->getIterator();
				foreach ($iterator as $innerItem) {
					/** @var \RecursiveDirectoryIterator $iterator */
					$files[] = $this->config['destination'] . '/' . $item . '/' . $iterator->getSubPathName();
				}

			} else {
				$files[] = $this->config['destination'] . '/' . $item;
			}
		}

		// Common files
		foreach ($this->config['template']['templates']['common'] as $item) {
			$files[] = $this->config['destination'] . '/' . $item;
		}

		// Optional files
		foreach ($this->config['template']['templates']['optional'] as $optional) {
			$files[] = $this->config['destination'] . '/' . $optional['filename'];
		}

		// Main files
		$masks = array_map(function ($config) {
			return preg_replace('~%[^%]*?s~', '*', $config['filename']);
		}, (array) $this->config['template']['templates']['main']);

		$filter = function ($item) use ($masks) {
			/** @var \SplFileInfo $item */
			foreach ($masks as $mask) {
				if (fnmatch($mask, $item->getFilename())) {
					return TRUE;
				}
			}
			return FALSE;
		};

		foreach (Nette\Utils\Finder::findFiles('*')->filter($filter)->from($this->config['destination']) as $item) {
			/** @var \SplFileInfo $item */
			$files[] = $item->getPathName();
		}

		return $files;
	}

}
