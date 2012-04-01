<?php

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
		echo str_replace(array('@header@', '@count@', '@option@', '@value@', '@error@', '@c'), '', static::getHelp());
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
	apigen @option@--config@c <@value@path@c> [options]
	apigen @option@--source@c <@value@dir@c|@value@file@c> @option@--destination@c <@value@dir@c> [options]

Options:
	@option@--config@c|@option@-c@c        <@value@file@c>      Config file
	@option@--source@c|@option@-s@c        <@value@dir@c|@value@file@c>  Source file or directory to parse (can be used multiple times)
	@option@--destination@c|@option@-d@c   <@value@dir@c>       Directory where to save the generated documentation
	@option@--extensions@c       <@value@list@c>      List of allowed file extensions, default "@value@php@c"
	@option@--exclude@c          <@value@mask@c>      Mask (case sensitive) to exclude file or directory from processing (can be used multiple times)
	@option@--skip-doc-path@c    <@value@mask@c>      Don't generate documentation for elements from file or directory with this (case sensitive) mask (can be used multiple times)
	@option@--skip-doc-prefix@c  <@value@value@c>     Don't generate documentation for elements with this (case sensitive) name prefix (can be used multiple times)
	@option@--charset@c          <@value@list@c>      Character set of source files, default "@value@auto@c"
	@option@--main@c             <@value@value@c>     Main project name prefix
	@option@--title@c            <@value@value@c>     Title of generated documentation
	@option@--base-url@c         <@value@value@c>     Documentation base URL
	@option@--google-cse-id@c    <@value@value@c>     Google Custom Search ID
	@option@--google-cse-label@c <@value@value@c>     Google Custom Search label
	@option@--google-analytics@c <@value@value@c>     Google Analytics tracking code
	@option@--template-config@c  <@value@file@c>      Template config file, default "@value@$defaultTemplateConfig@c"
	@option@--allowed-html@c     <@value@list@c>      List of allowed HTML tags in documentation, default "@value@b,i,a,ul,ol,li,p,br,var,samp,kbd,tt@c"
	@option@--groups@c           <@value@value@c>     How should elements be grouped in the menu. Default value is "@value@auto@c" (namespaces if available, packages otherwise)
	@option@--autocomplete@c     <@value@list@c>      Element types for search input autocomplete. Default value is "@value@classes,constants,functions@c"
	@option@--access-levels@c    <@value@list@c>      Generate documentation for methods and properties with given access level, default "@value@public,protected@c"
	@option@--internal@c         <@value@yes@c|@value@no@c>    Generate documentation for elements marked as internal and display internal documentation parts, default "@value@no@c"
	@option@--php@c              <@value@yes@c|@value@no@c>    Generate documentation for PHP internal classes, default "@value@yes@c"
	@option@--tree@c             <@value@yes@c|@value@no@c>    Generate tree view of classes, interfaces, traits and exceptions, default "@value@yes@c"
	@option@--deprecated@c       <@value@yes@c|@value@no@c>    Generate documentation for deprecated elements, default "@value@no@c"
	@option@--todo@c             <@value@yes@c|@value@no@c>    Generate documentation of tasks, default "@value@no@c"
	@option@--source-code@c      <@value@yes@c|@value@no@c>    Generate highlighted source code files, default "@value@yes@c"
	@option@--download@c         <@value@yes@c|@value@no@c>    Add a link to download documentation as a ZIP archive, default "@value@no@c"
	@option@--report@c           <@value@file@c>      Save a checkstyle report of poorly documented elements into a file
	@option@--wipeout@c          <@value@yes@c|@value@no@c>    Wipe out the destination directory first, default "@value@yes@c"
	@option@--quiet@c            <@value@yes@c|@value@no@c>    Don't display scaning and generating messages, default "@value@no@c"
	@option@--progressbar@c      <@value@yes@c|@value@no@c>    Display progressbars, default "@value@yes@c"
	@option@--colors@c           <@value@yes@c|@value@no@c>    Use colors, default "@value@no@c" on Windows, "@value@yes@c" on other systems
	@option@--update-check@c     <@value@yes@c|@value@no@c>    Check for update, default "@value@yes@c"
	@option@--debug@c            <@value@yes@c|@value@no@c>    Display additional information in case of an error, default "@value@no@c"
	@option@--help@c|@option@-h@c                      Display this help

Only source and destination directories are required - either set explicitly or using a config file. Configuration parameters passed via command line have precedence over parameters from a config file.

Boolean options (those with possible values @value@yes@c|@value@no@c) do not have to have their values defined explicitly. Using @option@--debug@c and @option@--debug@c=@value@yes@c is exactly the same.

Some options can have multiple values. You can do so either by using them multiple times or by separating values by a comma. That means that writing @option@--source@c=@value@file1.php@c @option@--source@c=@value@file2.php@c or @option@--source@c=@value@file1.php,file2.php@c is exactly the same.

Files or directories specified by @option@--exclude@c will not be processed at all.
Elements from files within @option@--skip-doc-path@c or with @option@--skip-doc-prefix@c will be parsed but will not have their documentation generated. However if classes have any child classes, the full class tree will be generated and their inherited methods, properties and constants will be displayed (but will not be clickable).

HELP;
	}
}
