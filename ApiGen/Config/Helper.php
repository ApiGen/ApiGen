<?php

/**
 * ApiGen 3.0dev - API documentation generator for PHP 5.3+
 *
 * Copyright (c) 2010-2011 David Grudl (http://davidgrudl.com)
 * Copyright (c) 2011-2012 Jaroslav Hanslík (https://github.com/kukulich)
 * Copyright (c) 2011-2012 Ondřej Nešpor (https://github.com/Andrewsville)
 *
 * For the full copyright and license information, please view
 * the file LICENSE.md that was distributed with this source code.
 */

namespace ApiGen\Config;

use ApiGen;
use ApiGen\ConsoleLogger;
use ApiGen\Environment;
use Nette\Config\Helpers;
use Nette\Config\Loader;

/**
 * Configuration helper.
 */
class Helper extends Helpers
{
	/**
	 * The default configuration file name.
	 *
	 * @var string
	 */
	const DEFAULT_CONFIG_FILENAME = 'apigen.neon';

	/**
	 * The default template configuration file name.
	 *
	 * @var string
	 */
	const DEFAULT_TEMPLATE_CONFIG_FILENAME = 'default/config.neon';

	/**
	 * Command line arguments.
	 *
	 * @var array
	 */
	private $cliArguments = array();

	/**
	 * Creates a helper instance.
	 *
	 * @param array $argv Command line arguments
	 */
	public function __construct(array $argv)
	{
		$this->cliArguments = Environment::getCliArguments($argv);
	}

	/**
	 * Returns command line arguments.
	 *
	 * @return array
	 */
	public function getCliArguments()
	{
		return $this->cliArguments;
	}

	/**
	 * Returns the if help should be displayed.
	 *
	 * @return boolean
	 */
	public function isHelpRequested()
	{
		if (empty($this->cliArguments) && !$this->defaultConfigExists()) {
			return true;
		}

		if (isset($this->cliArguments['h']) || isset($this->cliArguments['help'])) {
			return true;
		}

		return false;
	}

	/**
	 * Prints out help.
	 */
	public static function printHelp()
	{
		echo static::getHelp();
	}

	/**
	 * Returns default configuration file path.
	 *
	 * @return string
	 */
	public static function getDefaultConfigPath()
	{
		return getcwd() . DIRECTORY_SEPARATOR . static::DEFAULT_CONFIG_FILENAME;
	}

	/**
	 * Returns templates directory path.
	 *
	 * @return string
	 */
	public static function getTemplatesDir()
	{
		return realpath(ApiGen\ROOT_PATH . '/templates/');
	}

	/**
	 * Returns default template configuration file path.
	 *
	 * @return string
	 */
	private static function getDefaultTemplateConfigPath()
	{
		return static::getTemplatesDir() . '/' . static::DEFAULT_TEMPLATE_CONFIG_FILENAME;
	}

	/**
	 * Checks if default configuration file exists.
	 *
	 * @return boolean
	 */
	public static function defaultConfigExists()
	{
		return is_file(static::getDefaultConfigPath());
	}

	/**
	 * Returns a file absolute path.
	 *
	 * @param string $relativePath File relative path
	 * @param array $baseDirectories List of base directories
	 * @return string|null
	 */
	public static function getAbsolutePath($relativePath, array $baseDirectories)
	{
		if (preg_match('~/|[a-z]:~Ai', $relativePath)) {
			// Absolute path already
			return $relativePath;
		}

		foreach ($baseDirectories as $directory) {
			$fileName = $directory . DIRECTORY_SEPARATOR . $relativePath;
			if (is_file($fileName)) {
				return realpath($fileName);
			}
		}

		return null;
	}

	/**
	 * Returns help.
	 *
	 * @return string
	 */
	public static function getHelp()
	{
		$defaultTemplateConfig = static::getDefaultTemplateConfigPath();

		return <<<"HELP"
Usage:
	apigen --config <path> [options]
	apigen --source <dir|file> --destination <dir> [options]

Options:
	--config|-c        <file>      Config file
	--source|-s        <dir|file>  Source file or directory to parse (can be used multiple times)
	--destination|-d   <dir>       Directory where to save the generated documentation
	--extensions       <list>      List of allowed file extensions, default "php"
	--exclude          <mask>      Mask (case sensitive) to exclude file or directory from processing (can be used multiple times)
	--skip-doc-path    <mask>      Don't generate documentation for elements from file or directory with this (case sensitive) mask (can be used multiple times)
	--skip-doc-prefix  <value>     Don't generate documentation for elements with this (case sensitive) name prefix (can be used multiple times)
	--charset          <list>      Character set of source files, default "auto"
	--main             <value>     Main project name prefix
	--title            <value>     Title of generated documentation
	--base-url         <value>     Documentation base URL
	--google-cse-id    <value>     Google Custom Search ID
	--google-cse-label <value>     Google Custom Search label
	--google-analytics <value>     Google Analytics tracking code
	--template-config  <file>      Template config file, default "$defaultTemplateConfig"
	--allowed-html     <list>      List of allowed HTML tags in documentation, default "b,i,a,ul,ol,li,p,br,var,samp,kbd,tt"
	--groups           <value>     How should elements be grouped in the menu. Default value is "auto" (namespaces if available, packages otherwise)
	--autocomplete     <list>      Element types for search input autocomplete. Default value is "@valuelasses,constants,functions"
	--access-levels    <list>      Generate documentation for methods and properties with given access level, default "public,protected"
	--internal         <yes|no>    Generate documentation for elements marked as internal and display internal documentation parts, default "no"
	--php              <yes|no>    Generate documentation for PHP internal classes, default "yes"
	--tree             <yes|no>    Generate tree view of classes, interfaces, traits and exceptions, default "yes"
	--deprecated       <yes|no>    Generate documentation for deprecated elements, default "no"
	--todo             <yes|no>    Generate documentation of tasks, default "no"
	--source-code      <yes|no>    Generate highlighted source code files, default "yes"
	--download         <yes|no>    Add a link to download documentation as a ZIP archive, default "no"
	--report           <file>      Save a checkstyle report of poorly documented elements into a file
	--wipeout          <yes|no>    Wipe out the destination directory first, default "yes"
	--quiet            <yes|no>    Don't display scaning and generating messages, default "no"
	--progressbar      <yes|no>    Display progressbars, default "yes"
	--colors           <yes|no>    Use colors, default "no" on Windows, "yes" on other systems
	--update-check     <yes|no>    Check for update, default "yes"
	--debug            <yes|no>    Display additional information in case of an error, default "no"
	--help|-h                      Display this help

Only source and destination directories are required - either set explicitly or using a config file. Configuration parameters passed via command line have precedence over parameters from a config file.

Boolean options (those with possible values yes|no) do not have to have their values defined explicitly. Using --debug and --debug=yes is exactly the same.

Some options can have multiple values. You can do so either by using them multiple times or by separating values by a comma. That means that writing --source=file1.php --source=file2.php or --source=file1.php,file2.php is exactly the same.

Files or directories specified by --exclude will not be processed at all.
Elements from files within --skip-doc-path or with --skip-doc-prefix will be parsed but will not have their documentation generated. However if classes have any child classes, the full class tree will be generated and their inherited methods, properties and constants will be displayed (but will not be clickable).

HELP;
	}
}
