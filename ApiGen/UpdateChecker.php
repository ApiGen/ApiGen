<?php

namespace ApiGen;

/**
 * pear.ApiGen.org update checker.
 */
class UpdateChecker implements IUpdateChecker
{
	/**
	 * Creates a checker instance.
	 *
	 * Tries to set the default socket timeout.
	 */
	public function __construct()
	{
		@ini_set('default_socket_timeout', 5);
	}

   /**
	 * Returns the newest version.
	 *
	 * @return string
	 */
	public function getNewestVersion()
	{
		$latestVersion = @file_get_contents('http://pear.apigen.org/rest/r/apigen/latest.txt');
		return false === $latestVersion ? null : trim($latestVersion);
	}
}
