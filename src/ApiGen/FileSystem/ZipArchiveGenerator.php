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
use Nette\Utils\Finder;
use Nette\Utils\Strings;
use RuntimeException;
use SplFileInfo;
use ZipArchive;


class ZipArchiveGenerator
{

	/**
	 * @var Configuration
	 */
	private $configuration;


	public function __construct(Configuration $configuration)
	{
		$this->configuration = $configuration;
	}


	public function generate()
	{
		if ( ! extension_loaded('zip')) {
			throw new RuntimeException('Extension zip is not loaded');
		}

		$archive = new ZipArchive;
		if ($archive->open($this->getArchivePath(), ZipArchive::CREATE) !== TRUE) {
			throw new RuntimeException('Could not create ZIP archive');
		}

		$destination = $this->configuration->getOption(CO::DESTINATION);
		$directory = $this->getWebalizedTitle();

		/** @var SplFileInfo $file */
		foreach (Finder::findFiles('*')->from($destination) as $file) {
			$relativePath = Strings::substring($file->getRealPath(), strlen($destination) + 1);
			$archive->addFile($file, $directory . '/' . $relativePath);
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
