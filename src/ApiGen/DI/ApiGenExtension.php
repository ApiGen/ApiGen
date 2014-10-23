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
use TokenReflection\Broker;


class ApiGenExtension extends CompilerExtension
{

	public function loadConfiguration()
	{
		$builder = $this->getContainerBuilder();

		// configuration
		$builder->addDefinition($this->prefix('configuration'))
			->setClass('ApiGen\Configuration\Configuration');

		// charset
		$builder->addDefinition($this->prefix('charsetConvertor'))
			->setClass('ApiGen\Charset\CharsetConvertor');

		// generator
		$builder->addDefinition($this->prefix('generator'))
			->setClass('ApiGen\Generator\Generator');

		$builder->addDefinition($this->prefix('elementResolver'))
			->setClass('ApiGen\Generator\Resolvers\ElementResolver');

		$builder->addDefinition($this->prefix('relativePathResolver'))
			->setClass('ApiGen\Generator\Resolvers\RelativePathResolver');

		$builder->addDefinition($this->prefix('scanner'))
			->setClass('ApiGen\Scanner\Scanner');

		$builder->addDefinition($this->prefix('markdown'))
			->setClass('Michelf\MarkdownExtra');

		$builder->addDefinition($this->prefix('markdownMarkup'))
			->setClass('ApiGen\Generator\Markups\MarkdownMarkup');

		$this->setupConsole();
		$this->setupEvents();
		$this->setupFileSystem();
		$this->setupFshl();
		$this->setupMetrics();
		$this->setupParser();
		$this->setupTemplate();
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


	private function setupConsole()
	{
		$builder = $this->getContainerBuilder();

		$application = $builder->addDefinition($this->prefix('application'))
			->setClass('ApiGen\Console\Application')
			->addSetup('injectServiceLocator')
			->addSetup('setEventManager');

		$builder->addDefinition($this->prefix('consoleIO'))
			->setClass('ApiGen\Console\IO');

		foreach ($this->loadFromFile(__DIR__ . '/commands.neon') as $i => $class) {
			if ( ! $this->isPhar() && $class === 'ApiGen\Command\SelfUpdateCommand') {
				continue;
			}

			$command = $builder->addDefinition($this->prefix('command.' . $i))
				->setClass($class);

			$application->addSetup('add', array('@' . $command->getClass()));
		}

		$builder->addDefinition($this->prefix('console.progressBar'))
			->setClass('ApiGen\Console\ProgressBar');
	}


	private function setupFileSystem()
	{
		$builder = $this->getContainerBuilder();

		$builder->addDefinition($this->prefix('finder'))
			->setClass('ApiGen\FileSystem\Finder');

		$builder->addDefinition($this->prefix('zip'))
			->setClass('ApiGen\FileSystem\Zip');

		$builder->addDefinition($this->prefix('wiper'))
			->setClass('ApiGen\FileSystem\Wiper');
	}


	private function setupFshl()
	{
		$builder = $this->getContainerBuilder();

		$builder->addDefinition($this->prefix('fshl.output'))
			->setClass('FSHL\Output\Html');

		$builder->addDefinition($this->prefix('fshl.lexter'))
			->setClass('FSHL\Lexer\Php');

		$builder->addDefinition($this->prefix('fshl.highlighter'))
			->setClass('FSHL\Highlighter')
			->addSetup('setLexer', array('@FSHL\Lexer\Php'));

		$builder->addDefinition($this->prefix('sourceCodeHighlighter'))
			->setClass('ApiGen\Generator\FshlSourceCodeHighlighter');
	}


	private function setupMetrics()
	{
		$builder = $this->getContainerBuilder();

		$builder->addDefinition($this->prefix('memoryLimitChecker'))
			->setClass('ApiGen\Metrics\SimpleMemoryLimitChecker');
	}


	private function setupParser()
	{
		$builder = $this->getContainerBuilder();

		$builder->addDefinition($this->prefix('parser'))
			->setClass('ApiGen\Parser\Parser');

		$backend = $builder->addDefinition($this->prefix('parser.backend'))
			->setClass('ApiGen\Parser\Broker\Backend');

		$builder->addDefinition($this->prefix('parser.broker'))
			->setClass('TokenReflection\Broker')
			->setArguments(array($backend,
				Broker::OPTION_DEFAULT & ~(Broker::OPTION_PARSE_FUNCTION_BODY | Broker::OPTION_SAVE_TOKEN_STREAM)
			));

		$builder->addDefinition($this->prefix('parser.result'))
			->setClass('ApiGen\Parser\ParserResult');
	}


	private function setupTemplate()
	{
		$builder = $this->getContainerBuilder();

		$builder->addDefinition($this->prefix('templateFactory'))
			->setClass('ApiGen\Templating\TemplateFactory');

		$latteFactory = $builder->addDefinition($this->prefix('latteFactory'))
			->setClass('Latte\Engine')
			->addSetup('setTempDirectory', array($builder->expand('%tempDir%/cache/latte')));

		foreach ($this->loadFromFile(__DIR__ . '/filters.neon') as $i => $class) {
			$filter = $builder->addDefinition($this->prefix('latte.filter.' . $i))
				->setClass($class);

			$latteFactory->addSetup('addFilter', array(NULL, array('@' . $filter->getClass(), 'loader')));
		}
	}


	/**
	 * @return bool
	 */
	private function isPhar()
	{
		return 'phar:' === substr(__FILE__, 0, 5);
	}

}
