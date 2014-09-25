<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\DI;

use ApiGen;
use Kdyby\Events\DI\EventsExtension;
use Nette\DI\CompilerExtension;
use Nette\DI\ServiceDefinition;
use Nette\DI\Statement;


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
		'groups' => 'auto',
		'autocomplete' => array('classes', 'constants', 'functions'),
		'accessLevels' => array('public', 'protected'),
		'internal' => FALSE,
		'php' => TRUE,
		'tree' => TRUE,
		'deprecated' => FALSE,
		'todo' => FALSE,
		'download' => FALSE,
		'wipeout' => TRUE,
		'debug' => NULL, // placeholder
		'markup' => 'markdown',
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

		$this->defaults['debug'] = $builder->parameters['debugMode'];

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

		// charset
		$builder->addDefinition($this->prefix('charsetConvertor'))
			->setClass('ApiGen\Charset\CharsetConvertor')
			->addSetup('setCharset', array(
					new Statement('(array) ?->?', array('@ApiGen\Configuration\Configuration', 'charset')))
			);

		// generator
		$builder->addDefinition($this->prefix('generator'))
			->setClass('ApiGen\Generator\HtmlGenerator');

		$builder->addDefinition($this->prefix('elementResolver'))
			->setClass('ApiGen\Generator\Resolvers\ElementResolver');

		$builder->addDefinition($this->prefix('relativePathResolver'))
			->setClass('ApiGen\Generator\Resolvers\RelativePathResolver');

		$builder->addDefinition($this->prefix('scanner'))
			->setClass('ApiGen\Generator\PhpScanner');

		// source code highlighter
		$builder->addDefinition($this->prefix('fshl.output'))
			->setClass('FSHL\Output\Html');

		$builder->addDefinition($this->prefix('fshl.lexter'))
			->setClass('FSHL\Lexer\Php');

		$builder->addDefinition($this->prefix('fshl.highlighter'))
			->setClass('FSHL\Highlighter')
			->addSetup('setLexer', array('@FSHL\Lexer\Php'));

		$builder->addDefinition($this->prefix('sourceCodeHighlighter'))
			->setClass('ApiGen\Generator\FshlSourceCodeHighlighter');

		// markup
		if ($config['markup'] === 'markdown') {
			$builder->addDefinition($this->prefix('markdown'))
				->setClass('Michelf\MarkdownExtra');

			$builder->addDefinition($this->prefix('markdownMarkup'))
				->setClass('ApiGen\Generator\Markups\MarkdownMarkup');

		} else {
			$builder->addDefinition($this->prefix('texy'))
				->setClass('Texy');

			$builder->addDefinition($this->prefix('texyMarkup'))
				->setClass('ApiGen\Generator\Markups\TexyMarkup')
				->addSetup('setup');
		}

		$this->setupMetrics();
		$this->setupEvents();
		$this->setupTemplate();

		// @todo: what for? removes system parameters!
		// $builder->parameters = $config;
	}


	private function setupEvents()
	{
		$builder = $this->getContainerBuilder();

		foreach ($this->loadFromFile(__DIR__ . '/events.neon') as $i => $class) {
			$builder->addDefinition($this->prefix('event.' . $i))
				->setClass($class)
				->addTag(EventsExtension::TAG_SUBSCRIBER);
		}
	}


	private function setupTemplate()
	{
		$builder = $this->getContainerBuilder();

		$builder->addDefinition($this->prefix('templateFactory'))
			->setClass('ApiGen\Templating\TemplateFactory');

		$builder->addDefinition($this->prefix('textFormatter'))
			->setClass('ApiGen\Templating\Filters\Helpers\TextFormatter');

		$latteFactory = $builder->addDefinition($this->prefix('latteFactory'))
			->setClass('Latte\Engine')
			->addSetup('setTempDirectory', array($builder->expand('%tempDir%/cache/latte')));

		foreach ($this->loadFromFile(__DIR__ . '/filters.neon') as $i => $class) {
			$filter = $builder->addDefinition($this->prefix('latte.filter.' . $i))
				->setClass($class);

			$latteFactory->addSetup('addFilter', array(NULL, array('@' . $filter->getClass(), 'loader')));
		}
	}


	private function setupMetrics()
	{
		$builder = $this->getContainerBuilder();

		$builder->addDefinition($this->prefix('memoryLimitChecker'))
			->setClass('ApiGen\Metrics\SimpleMemoryLimitChecker');

		$builder->addDefinition($this->prefix('elapsedTimeAndMemory'))
			->setClass('ApiGen\Metrics\ElapsedTimeAndMemory');
	}

}
