<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\DI;

use ApiGen\Parser\Broker\Backend;
use Nette\DI\CompilerExtension;
use TokenReflection\Broker;


class ParserExtension extends CompilerExtension
{

	public function loadConfiguration()
	{
		$builder = $this->getContainerBuilder();
		$config = $this->loadFromFile(__DIR__ . '/parser.services.neon');
		$this->compiler->parseServices($builder, $config);

		$backend = $builder->addDefinition($this->prefix('backend'))
			->setClass(Backend::class);

		$builder->addDefinition($this->prefix('broker'))
			->setClass(Broker::class)
			->setArguments([
				$backend,
				Broker::OPTION_DEFAULT & ~(Broker::OPTION_PARSE_FUNCTION_BODY | Broker::OPTION_SAVE_TOKEN_STREAM)
			]);
	}

}
