<?php

namespace ApiGen;

/**
 * Interface for services checking for the newest version.
 */
interface IUpdateChecker
{
	/**
	 * Returns the newest version.
	 *
	 * @return string
	 */
	public function getNewestVersion();
}
