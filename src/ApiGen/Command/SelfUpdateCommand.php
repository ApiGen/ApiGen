<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Command;

use ApiGen\ApiGen;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class SelfUpdateCommand extends Command
{

	const MANIFEST_URL = 'http://apigen.org/manifest.json';


	protected function configure()
	{
		$this->setName('self-update')
			->setAliases(['selfupdate'])
			->setDescription('Updates apigen.phar to the latest version')
			->setHelp(<<<EOT
The <info>self-update</info> command checks apigen.org for newer
version of ApiGen and if found, installs it.

<info>php apigen.phar self-update</info>
EOT
			);
	}


	protected function execute(InputInterface $input, OutputInterface $output)
	{
		try {
			$manifest = file_get_contents(self::MANIFEST_URL);
			$item = json_decode($manifest);

			if (ApiGen::VERSION === $item->version) {
				$output->writeln('<info>You are already using most recent ApiGen version</info>');
				return 0;
			}

			$this->downloadFile($output, $item);
			$this->makeFileExecutable($output);

			$output->writeln('<info>ApiGen updated!</info>');
			return 0;

		} catch (\Exception $e) {
			$output->writeln(PHP_EOL . '<error>' . $e->getMessage() . '</error>');
			return 1;
		}
	}


	/**
	 * @param OutputInterface $output
	 * @param $item
	 */
	private function downloadFile(OutputInterface $output, $item)
	{
		$output->writeln('<info>Downloading ApiGen ' . $item->version . '...</info>');
		file_put_contents($this->getTempFilename(), file_get_contents($item->url));
		$this->validateFileChecksum($output, $item);
		rename($this->getTempFilename(), $this->getLocalFilename());
		$this->validatePhar();
	}


	/**
	 * @param OutputInterface $output
	 * @param $item
	 * @throws \Exception
	 */
	private function validateFileChecksum(OutputInterface $output, $item)
	{
		$output->writeln('<info>Checking file checksum...</info>');
		if ($item->sha1 !== sha1_file($this->getTempFilename())) {
			unlink($this->getTempFilename());
			throw new \Exception('The download file was corrupted.');
		}
	}


	private function validatePhar()
	{
		if ( ! ini_get('phar.readonly')) {
			$phar = new \Phar($this->getLocalFilename());
			unset($phar);
		}
	}


	private function makeFileExecutable(OutputInterface $output)
	{
		$output->writeln('<info>Making ApiGen executable...</info>');
		@chmod($this->getLocalFilename(), 0755);
	}


	/**
	 * @return string
	 */
	private function getTempFilename()
	{
		return basename($this->getLocalFilename(), '.phar') . '-temp.phar';
	}


	/**
	 * @return string
	 * @throws \Exception
	 */
	private function getLocalFilename()
	{
		$localFilename = realpath($_SERVER['argv'][0]) ?: $_SERVER['argv'][0];
		$tmpDir = dirname($localFilename);

		if ( ! is_writable($tmpDir)) {
			throw new \Exception('ApiGen update failed: the "' . $tmpDir . '" directory used to download'
				. ' the temp file could not be written');
		}
		if ( ! is_writable($localFilename)) {
			throw new \Exception('ApiGen update failed: the "' . $localFilename . '" file could not be written');
		}

		return $localFilename;
	}

}
