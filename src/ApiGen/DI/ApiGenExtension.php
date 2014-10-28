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
use TokenReflection\Broker;


class ApiGenExtension extends CompilerExtension
{

	public function loadConfiguration()
	{
		$builder = $this->getContainerBuilder();

		$services = $this->loadFromFile(__DIR__ . DS . 'services.neon');
		$this->compiler->parseServices($builder, $services);

		$this->setupParser();
		$this->setupTemplating();
	}


	public function beforeCompile()
	{
		$builder = $this->getContainerBuilder();

		$application = $builder->getDefinition($builder->getByType('ApiGen\Console\Application'));
		foreach ($builder->findByType('Symfony\Component\Console\Command\Command') as $command) {
			if ( ! $this->isPhar() && $command === 'ApiGen\Command\SelfUpdateCommand') {
				continue;
			}
			$application->addSetup('add', array('@' . $command));
		}

		foreach ($builder->findByType('Kdyby\Events\Subscriber') as $event) {
			$builder->getDefinition($event)
				->addTag(EventsExtension::TAG_SUBSCRIBER);
		}
	}


	private function setupParser()
	{
		$builder = $this->getContainerBuilder();

		$backend = $builder->addDefinition($this->prefix('parser.backend'))
			->setClass('ApiGen\Parser\Broker\Backend');

		$builder->addDefinition($this->prefix('parser.broker'))
			->setClass('TokenReflection\Broker')
			->setArguments(array(
				$backend,
				Broker::OPTION_DEFAULT & ~(Broker::OPTION_PARSE_FUNCTION_BODY | Broker::OPTION_SAVE_TOKEN_STREAM)
			));
	}


	private function setupTemplating()
	{
		$builder = $this->getContainerBuilder();

		$latteFactory = $builder->addDefinition($this->prefix('latteFactory'))
			->setClass('Latte\Engine')
			->addSetup('setTempDirectory', array($builder->expand('%tempDir%/cache/latte')));

		foreach ($builder->findByType('ApiGen\Templating\Filters\Filters') as $filter) {
			$latteFactory->addSetup('addFilter', array(NULL, array('@' . $filter, 'loader')));
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
