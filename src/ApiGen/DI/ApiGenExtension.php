<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\DI;

use ApiGen;
use Nette\DI\CompilerExtension;


class ApiGenExtension extends CompilerExtension
{
	/**
	 * @var array
	 */
	protected $defaults = array(
		'config' => '',
		'source' => array(),
		'destination' => '',
		'extensions' => array('php'),
		'exclude' => array(),
		'skipDocPath' => array(),
		'skipDocPrefix' => array(),
		'charset' => array('auto'),
		'main' => '',
		'title' => '',
		'baseUrl' => '',
		'googleCseId' => '',
		'googleAnalytics' => '',
		'allowedHtml' => array('b', 'i', 'a', 'ul', 'ol', 'li', 'p', 'br', 'var', 'samp', 'kbd', 'tt'),
		'groups' => 'auto',
		'autocomplete' => array('classes', 'constants', 'functions'),
		'accessLevels' => array('public', 'protected'),
		'internal' => FALSE,
		'php' => TRUE,
		'tree' => TRUE,
		'deprecated' => FALSE,
		'todo' => FALSE,
		'download' => FALSE,
		'report' => '',
		'wipeout' => TRUE,
		'debug' => FALSE,
		// template
		'templateConfig' => '',
		'template' => array(
			'resources' => array(),
			'templates' => array(
				'common' => array(),
				'optional' => array()
			)
		)
	);

	/**
	 * @var ApiGen\Configuration\Validator
	 */
	protected $configurationValidator;

	/**
	 * @var ApiGen\Configuration\Composer
	 */
	protected $configurationComposer;

	/**
	 * @var ApiGen\Configuration\Helper
	 */
	private $configurationHelper;


	public function __construct()
	{
		$this->configurationValidator = new ApiGen\Configuration\Validator;
		$this->configurationComposer = new ApiGen\Configuration\Composer;
		$this->configurationHelper = new ApiGen\Configuration\Helper;
		$this->defaults['templateConfig'] = APIGEN_ROOT_PATH . '/templates/' . ApiGen\Configuration\Helper::DEFAULT_TEMPLATE_CONFIG_FILENAME;
	}


	public function loadConfiguration()
	{
		$builder = $this->getContainerBuilder();

		// parameters (@todo: resolvers: default, cli, config?)
		$config = $this->getConfig($this->defaults);
		$config = $this->configurationComposer->addCliArguments($config);
		$config = $this->configurationComposer->addConfigFileOptions($config, $this);

		// sanitize
		$config = $this->configurationHelper->sanitizeConfigOptions($config);

		// template
		$config = $this->configurationComposer->addTemplateOptions($config, $this);

		$this->configurationValidator->validateConfig($config);

		// configuration
		$builder->addDefinition($this->prefix('configuration'))
			->setClass('ApiGen\Configuration\Configuration')
			->setArguments(array($config));

		// application
		$builder->addDefinition($this->prefix('application'))
			->setClass('ApiGen\Application\Application');

		$builder->addDefinition($this->prefix('configurationHelper'))
			->setClass('ApiGen\Configuration\Helper');

		// console
		$builder->addDefinition($this->prefix('console.logger'))
			->setClass('ApiGen\Console\ConsoleLogger');

		$builder->addDefinition($this->prefix('console.progressBar'))
			->setClass('ApiGen\Console\SimpleProgressBar');

		$builder->addDefinition($this->prefix('console.helper'))
			->setClass('ApiGen\Console\Helper');

		$builder->addDefinition($this->prefix('errorHandler'))
			->setClass('ApiGen\LogErrorHandler');

		// generator
		$builder->addDefinition($this->prefix('generator'))
			->setClass('ApiGen\Generator\HtmlGenerator');

		$builder->addDefinition($this->prefix('scanner'))
			->setClass('ApiGen\Generator\PhpScanner');

		// @todo: what for? removes system parameters!
		// $builder->parameters = $config;
	}

}
