<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\FileSystem;

use ApiGen\Configuration\Configuration;
use ApiGen\Configuration\ConfigurationOptions as CO;
use Nette;
use Nette\Utils\Strings;
use RuntimeException;
use ZipArchive;


class ZipArchiveGenerator
{

	/**
	 * @var Configuration
	 */
	private $configuration;

	/**
	 * @var Finder
	 */
	private $finder;


	public function __construct(Configuration $configuration,  Finder $finder)
	{
		$this->finder = $finder;
		$this->configuration = $configuration;
	}


	public function generate()
	{
		if ( ! extension_loaded('zip')) {
			throw new RuntimeException('Extension zip is not loaded');
		}

		$archive = new ZipArchive;
		if ($archive->open($this->getArchivePath(), ZipArchive::CREATE) !== TRUE) {
			throw new RuntimeException('Could not open ZIP archive');
		}

		$directory = $this->getWebalizedTitle();
		$destinationLength = strlen($this->configuration->getOption(CO::DESTINATION));
		foreach ($this->finder->findGeneratedFiles() as $file) {
			if (is_file($file)) {
				$archive->addFile($file, $directory . '/' . substr($file, $destinationLength + 1));
			}
		}

		if ($archive->close() === FALSE) {
			throw new RuntimeException('Could not save ZIP archive');
		}
	}


	/**
	 * @return string
	 */
	public function getArchivePath()
	{
		return $this->configuration->getOption(CO::DESTINATION) . '/' . $this->getWebalizedTitle() . '.zip';
	}


	/**
	 * @return string
	 */
	private function getWebalizedTitle()
	{
		return Strings::webalize($this->getTitle(), NULL, FALSE);
	}


	/**
	 * @return string
	 */
	private function getTitle()
	{
		$title = $this->configuration->getOption(CO::TITLE);
		return $title . ' API documentation';
	}

}
